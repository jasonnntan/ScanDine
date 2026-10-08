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

        return view('order.menu',[
            'table' => $table,
            'categories' => $categories,
        ]);
    }

    //what to put inside the brackets - 2 rules: the label, and what to give back if is empty or not exist yet
    //this also dont understand
    public function addToCart(Request $request, $qr_token)
    {
    $cart = session()->get('cart', []);

    $foodItemId = $request->food_item_id;
    $quantity = $request->quantity;

    if (isset($cart[$foodItemId])) {
        $cart[$foodItemId]['quantity'] += $quantity;
    } else {
        $foodItem = \App\Models\FoodItem::find($foodItemId);
        $cart[$foodItemId] = [
            'name' => $foodItem->name,
            'price' => $foodItem->price,
            'quantity' => $quantity,
        ];
    }

    session()->put('cart', $cart);

    if ($request->wantsJson()) {
        return response()->json(['cart' => $cart]);
    }

    return redirect()->route('order.menu', $qr_token)->with('success', 'Item added to cart!');
    }

    //view cart
    public function viewCart($qr_token)
    {
        $table = RestaurantTable::where('qr_token', $qr_token)->firstOrFail();
        $cart = session()->get('cart', []);

        $total = 0;
        foreach ($cart as $item){
            $total += $item['price'] * $item['quantity'];
        }

        return view('order.cart', [
            'table' => $table,
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    //
    public function placeOrder(Request $request, $qr_token)
{
    $table = RestaurantTable::where('qr_token', $qr_token)->firstOrFail();
    $cart = session()->get('cart', []);

    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $order = \App\Models\CustomerOrder::create([
        'restaurant_table_id' => $table->id,
        'customer_name' => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'customer_note' => $request->customer_note,
        'total_amount' => $total,
        'order_status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    foreach ($cart as $foodItemId => $item) {
        \App\Models\OrderItem::create([
            'customer_order_id' => $order->id,
            'food_item_id' => $foodItemId,
            'item_name_snapshot' => $item['name'],
            'unit_price' => $item['price'],
            'quantity' => $item['quantity'],

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

    if(isset($cart[$foodItemId])){
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
            $cart[$foodItemId]['quantity'] = $quantity;
        }
    }

    session()->put('cart', $cart);

    return response()->json(['cart' => $cart]);
}
}
