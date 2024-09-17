<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Manufacture;
use App\Models\Color;
use App\Models\Size;

use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!\App::runningInConsole()) {
            // chu de
            View::share('post_categories', PostCategory::orderBy('sort_id', 'asc')->get());

            // the bai viet
            View::share('tags', Tag::all());

            View::share('colors', Color::all());
            View::share('sizes', Size::all());

            // bai viet
            View::share('new_posts', Post::orderBy('created_at', 'desc')->take(12)->get());

            // nhom san pham
            View::share('product_categories', ProductCategory::orderBy('name', 'asc')->get());

            // nha san xuat
            View::share('manufacturers', Manufacture::orderBy('name', 'asc')->get());

            // new product order by created at desc
            View::share('new_products', Product::where('status', 1)->where('is_main', 1)->orderBy('created_at', 'desc')->take(6)->get());
            View::share('price_sale_min', Product::where('status', 1)->min('price_sale'));
            View::share('price_sale_max', Product::where('status', 1)->max('price_sale'));

            // bai viet moi nhat
            View::share('recent_posts', Post::orderBy('created_at', 'desc')->take(5)->get());

            // san pham duoc quan tam nhieu nhat
            View::share('most_interesting_products', Product::where('status', 1)->orderBy('view_count', 'desc')->take(12)->get());

            // san pham ban nhieu nhat
            View::share('most_sold_products', Product::where('status', 1)->orderBy('bought', 'desc')->take(12)->get());
        }
    }
}
