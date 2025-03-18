<?php

namespace App\Http;

use App\Models\Message;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Shipping;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class Helper
{
    // Get unread messages
    public static function messageList()
    {
        return Message::whereNull('read_at')->orderBy('created_at', 'desc')->get();
    }

    // Get all categories with parent-child relationship
    public static function getAllCategory()
    {
        $category = new Category();
        return $category->getAllParentWithChild();
    }

    // Get categories for the header menu (with parent-child relationships)
    public static function getHeaderCategory()
    {
        $category = new Category();
        $menu = $category->getAllParentWithChild();

        if ($menu) {
            echo '<li><a href="javascript:void(0);">Category<i class="ti-angle-down"></i></a><ul class="dropdown border-0 shadow">';
            
            foreach ($menu as $cat_info) {
                if ($cat_info->child_cat->count() > 0) {
                    echo '<li><a href="' . route('product-cat', $cat_info->slug) . '">' . $cat_info->title . '</a>';
                    echo '<ul class="dropdown sub-dropdown border-0 shadow">';
                    foreach ($cat_info->child_cat as $sub_menu) {
                        echo '<li><a href="' . route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]) . '">' . $sub_menu->title . '</a></li>';
                    }
                    echo '</ul></li>';
                } else {
                    echo '<li><a href="' . route('product-cat', $cat_info->slug) . '">' . $cat_info->title . '</a></li>';
                }
            }
            echo '</ul></li>';
        }
    }

    // Get list of product categories
    public static function productCategoryList($option = 'all')
    {
        if ($option == 'all') {
            return Category::orderBy('id', 'DESC')->get();
        }
        return Category::has('products')->orderBy('id', 'DESC')->get();
    }

    // Get list of post tags
    public static function postTagList($option = 'all')
    {
        if ($option == 'all') {
            return PostTag::orderBy('id', 'desc')->get();
        }
        return PostTag::has('posts')->orderBy('id', 'desc')->get();
    }

    // Get list of post categories
    public static function postCategoryList($option = 'all')
    {
        if ($option == 'all') {
            return PostCategory::orderBy('id', 'DESC')->get();
        }
        return PostCategory::has('posts')->orderBy('id', 'DESC')->get();
    }

    // Get cart count for the user
    public static function cartCount($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('quantity');
        }
        return 0;
    }

    // Get all products from cart for the user
    public static function getAllProductFromCart($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::with('product')->where('user_id', $user_id)->where('order_id', null)->get();
        }
        return 0;
    }

    // Get total price of the cart for the user
    public static function totalCartPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Cart::where('user_id', $user_id)->where('order_id', null)->sum('price');
        }
        return 0;
    }

    // Get wishlist count for the user
    public static function wishlistCount($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('quantity');
        }
        return 0;
    }

    // Get all products from wishlist for the user
    public static function getAllProductFromWishlist($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::with('product')->where('user_id', $user_id)->where('cart_id', null)->get();
        }
        return 0;
    }

    // Get total price of the wishlist for the user
    public static function totalWishlistPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('price');
        }
        return 0;
    }

    // Get grand total price including shipping and coupon
    public static function grandPrice($id, $user_id)
    {
        $order = Order::find($id);
        
        if ($order) {
            $shipping_price = (float)$order->shipping->price;
            $order_price = self::orderPrice($id, $user_id);
            return number_format((float)($order_price + $shipping_price), 2, '.', '');
        }

        return 0;
    }

    // Get earnings for the admin per month
    public static function earningPerMonth()
    {
        $month_data = Order::where('status', 'delivered')->get();
        $price = 0;
        foreach ($month_data as $data) {
            $price += $data->cart_info->sum('price');
        }
        return number_format((float)$price, 2, '.', '');
    }

    // Get shipping details
    public static function shipping()
    {
        return Shipping::orderBy('id', 'DESC')->get();
    }
}

