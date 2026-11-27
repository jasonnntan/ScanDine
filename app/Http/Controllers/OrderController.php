<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CustomerOrder;
use App\Models\FoodItem;
use App\Models\OrderItem;
use App\Models\RestaurantTable;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function menu($qr_token)
    {
        $table = RestaurantTable::where('qr_token', $qr_token)->firstOrFail();
        $categories = Category::with('foodItems')->get();
        $cart = session()->get('cart', []);

        return view('order.menu', [
            'table' => $table,
            'categories' => $categories,
            'setting' => Setting::current(),
            'cart' => $cart,
        ]);
    }

    public function addToCart(Request $request, $qr_token)
    {
        $foodItemId = $request->food_item_id;
        $foodItem = is_scalar($foodItemId) ? FoodItem::find($foodItemId) : null;
        $quantity = (int) $request->quantity;

        $error = null;

        if (!$foodItem || $foodItem->status !== 'available') {
            $error = 'Sorry, this item is not available right now.';
        } elseif ($quantity < 1) {
            $error = 'Please choose a quantity of 1 or more.';
        }

        if ($error) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $error], 422);
            }

            return redirect()->route('order.menu', $qr_token)->with('error', $error);
        }

        $cart = session()->get('cart', []);
        $current = isset($cart[$foodItem->id]) ? $cart[$foodItem->id]['quantity'] : 0;

        $cart[$foodItem->id] = [
            'name' => $foodItem->name,
            'price' => $foodItem->price,
            'quantity' => min($current + $quantity, 99),
        ];

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart]);
        }

        return redirect()->route('order.menu', $qr_token)->with('success', 'Item added to cart!');
    }

    public function placeOrder(Request $request, $qr_token)
    {
        $table = RestaurantTable::where('qr_token', $qr_token)->firstOrFail();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Your cart is empty.'], 422);
            }

            return redirect()->route('order.menu', $qr_token);
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $itemNotes = (array) $request->input('item_notes', []);
        $cleanName = $this->cleanText($request->customer_name, 100);
        $cleanPhone = $this->cleanText($request->customer_phone, 20);
        $cleanNote = $this->cleanText($request->customer_note, 500);

        $order = DB::transaction(function () use ($table, $cart, $total, $itemNotes, $cleanName, $cleanPhone, $cleanNote) {
            $nextDailyNumber = (CustomerOrder::whereDate('created_at', today())
                ->lockForUpdate()
                ->max('daily_order_number') ?? 0) + 1;

            $newOrder = CustomerOrder::create([
                'daily_order_number' => $nextDailyNumber,
                'restaurant_table_id' => $table->id,
                'customer_name' => $cleanName,
                'customer_phone' => $cleanPhone,
                'customer_note' => $cleanNote,
                'total_amount' => $total,
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($cart as $foodItemId => $item) {
                OrderItem::create([
                    'customer_order_id' => $newOrder->id,
                    'food_item_id' => $foodItemId,
                    'item_name_snapshot' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'item_note' => $this->cleanText($itemNotes[$foodItemId] ?? null, 200),
                ]);
            }

            return $newOrder;
        });

        session()->forget('cart');

        $displayNumber = $order->daily_order_number ?? $order->id;

        if ($request->wantsJson()) {
            return response()->json([
                'orderId' => $order->id,
                'dailyOrderNumber' => $displayNumber,
                'redirectUrl' => route('order.confirmation', $order->id),
            ]);
        }

        return redirect()->route('order.confirmation', $order->id)->with('success', 'Order #' . $displayNumber . ' placed successfully!');
    }

    public function confirmation($orderId)
    {
        $order = CustomerOrder::with(['orderItems', 'restaurantTable'])->findOrFail($orderId);

        return view('order.confirmation', [
            'order' => $order,
        ]);
    }

    public function viewCartJson($qr_token)
    {
        $cart = session()->get('cart', []);

        return response()->json(['cart' => $cart]);
    }

    public function removeFromCart(Request $request, $qr_token)
    {
        $cart = session()->get('cart', []);
        $foodItemId = $request->food_item_id;

        if (isset($cart[$foodItemId])) {
            unset($cart[$foodItemId]);
        }

        session()->put('cart', $cart);

        return response()->json(['cart' => $cart]);
    }

    public function updateCartQuantity(Request $request, $qr_token)
    {
        $cart = session()->get('cart', []);
        $foodItemId = $request->food_item_id;
        $quantity = (int) $request->quantity;

        if (isset($cart[$foodItemId])) {
            if ($quantity <= 0) {
                unset($cart[$foodItemId]);
            } else {
                $cart[$foodItemId]['quantity'] = min($quantity, 99);
            }
        }

        session()->put('cart', $cart);

        return response()->json(['cart' => $cart]);
    }

    private function cleanText($value, $max)
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, $max);
    }
}
