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


}
