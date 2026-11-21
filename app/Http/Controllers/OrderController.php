<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantTable;
use App\Models\Category;

class OrderController extends Controller
{
    public function menu($qr_token)
    {
        $table = RestaurantTable::where('qr_token', $qr_token)->firstOrFail();
        $categories = Category::with('foodItems')->get();

        return view('order.menu', [
            'table' => $table,
            'categories' => $categories,
            'setting' => \App\Models\Setting::current(),
        ]);
    }

    public function addToCart(Request $request, $qr_token)
    {
        $foodItemId = $request->food_item_id;
        $foodItem = is_scalar($foodItemId) ? \App\Models\FoodItem::find($foodItemId) : null;
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

        $order = \App\Models\CustomerOrder::create([
            'restaurant_table_id' => $table->id,
            'customer_name' => $this->cleanText($request->customer_name, 100),
            'customer_phone' => $this->cleanText($request->customer_phone, 20),
            'customer_note' => $this->cleanText($request->customer_note, 500),
            'total_amount' => $total,
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $itemNotes = (array) $request->input('item_notes', []);

        foreach ($cart as $foodItemId => $item) {
            \App\Models\OrderItem::create([
                'customer_order_id' => $order->id,
                'food_item_id' => $foodItemId,
                'item_name_snapshot' => $item['name'],
                'unit_price' => $item['price'],
                'quantity' => $item['quantity'],
                'item_note' => $this->cleanText($itemNotes[$foodItemId] ?? null, 200),
            ]);
        }

        session()->forget('cart');

        if ($request->wantsJson()) {
            return response()->json(['orderId' => $order->id]);
        }

        return redirect()->route('order.confirmation', $order->id)->with('success', 'Order placed successfully!');
    }

    public function confirmation($orderId)
    {
        $order = \App\Models\CustomerOrder::with('orderItems')->findOrFail($orderId);

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
                $cart[$foodItemId]['quantity'] = min($quantity,99);
            }
        }

        session()->put('cart', $cart);

        return response()->json(['cart' => $cart]);
    }

    // trims text, returns null when empty, and cuts it to a maximum length
    private function cleanText($value, $max)
    {
        if (!is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, $max);
    }
}
