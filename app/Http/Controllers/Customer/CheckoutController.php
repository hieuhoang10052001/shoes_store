<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;
use Session;
use Validator;
use App\Models\Product;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Mail;
use App\Mail\MailOrderSuccess;
use Illuminate\Support\Facades\Config;

class CheckoutController extends Controller
{
    public function index()
    {
        if (Session::has('cart')) {
            $cart = Session::get('cart');

            foreach ($cart as $id => $product) {
                $item = Product::where('id', $id)->first();
                if ($product['qty'] > $item->quantity) {
                    return view('shopping_cart', ['limit' => 'Sản phẩm ' . $product['name'] . ' không đáp ứng đủ số lượng! Sản phẩm này hiện có số lượng là ' . $item->quantity . '.']);
                }
            }

            $order_id = "ORD" . "" . date('YmdHis') . strtoupper(str_random(3));
            foreach ($cart as $id => $product) {
                $data = [];
                $data['order_id'] = $order_id;
                $data['product_id'] = $product['id'];
                $data['name'] = $product['name'];
                $data['slug'] = $product['slug'];
                $data['code'] = $product['code'];
                $data['image'] = $product['image'];
                $data['price'] = $product['price'];
                $data['price_sale'] = $product['price_sale'];
                $data['quantity'] = (int)$product['qty'];
                $data['size_id'] = (int)$product['size_id'];

                $order = Order::create($data);
            }

            $orders = Order::where('status', 0)->where('order_id', $order_id)->get();
            return view('checkout', ['orders' => $orders, 'order_id' => $order_id]);
        }
        return view('checkout');
    }

    public function webhookBack(Request $request){
        $payload = $request->all();
        $vnp_SecureHash = $payload['vnp_SecureHash'];
        $inputData = array();
        foreach ($payload as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        // vnp_Amount=24000000&vnp_BankCode=NCB&vnp_BankTranNo=VNP14522572&vnp_CardType=ATM\
        // &vnp_OrderInfo=Thanh+toan%3AORD20240720193310KFQ&vnp_PayDate=20240720193953&vnp_ResponseCode=00&vnp_TmnCode=P4AGRLY7
        // &vnp_TransactionNo=14522572&vnp_TransactionStatus=00&vnp_TxnRef=ORD20240720193310KFQ
        $vnp_HashSecret = config('app.vnp_HashSecret');
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $order = Transaction::where('order_id', $payload['vnp_TxnRef'])->first();

        if ($secureHash == $vnp_SecureHash) {
            $transactionData = [
                'notes' => $payload['vnp_ResponseCode'] == '00' ? 'Giao dịch thành công' : 'Giao dịch không thành công',
                'is_pay' => $payload['vnp_ResponseCode'] == '00' ? 1 : 0,
            ];
            DB::table('transactions')->where('order_id', $order->order_id)->update($transactionData);
            
            if ($order->customer_id) {
                $customer = Customer::find($order->customer_id);
                if ($customer && $customer->email) {
                    $orderOk = Order::select('orders.*', 'sizes.name as sizeName', 'colors.name as colorName')
                    ->join('products', 'products.id', '=', 'orders.product_id')
                    ->join('colors', 'colors.id', '=', 'products.color_id')
                    ->join('sizes', 'sizes.id', '=', 'orders.size_id')
                    ->where('orders.order_id', $order->order_id)->get();
                    Mail::mailer('smtp')->to($customer->email)->send(new MailOrderSuccess($orderOk, $customer->name));
                }
            }

            return redirect('/checkout/order-received/'.$order->order_id);
        }

        return view('500');
    }

    public function order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone_number' => 'required|max:11',
            'address' => 'required',
            'payment_method' => 'required',
        ], [
            'name.required' => 'Tên khách hàng không được để trống',
            'name.max' => 'Tên khách hàng không được nhiều hơn 255 kí tự',
            'phone_number.required' => 'Số điện thoại không được để trống',
            'address.required' => 'Bạn chưa nhập địa chỉ',
            'phone_number.max' => 'Số điện thoại không quá 11 số',
            'payment_method.required' => 'Phương thức thanh toán không được để trống',
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $data = [
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'customer_notes' => $request->note,
        ];

        if ($request->customer_id) {
            $data['customer_id'] = $request->customer_id;

            if ($request->score_awards == 1) {
                $score = DB::table('customers')->where('id', $data['customer_id'])->value('score_awards');

                if (!$request->score_awards_payment || (float) $request->score_awards_payment <= 0 || (float) $request->score_awards_payment > $score) {
                    return Redirect::back()->withErrors('Số điểm thanh toán không hợp lệ!');
                }

                $input_score = (float) $request->score_awards_payment;

                if ($input_score <= $data['amount']) {
                    $data['amount'] -= $input_score;
                    $data['score_awards'] = $input_score;
                } else {
                    $data['score_awards'] = $data['amount'];
                    $data['amount'] = 0;
                }

                DB::table('customers')->where('id', $data['customer_id'])->decrement('score_awards', $data['score_awards']);
            }
        }

        $order = Transaction::updateOrCreate(['order_id' => $data['order_id']], $data);

        if (!$order) {
            return view('500');
        }

        if (Session::has('cart')) {
            Session::forget('cart');
        }

        $products_order_detail = DB::table('orders')->where('order_id', $order->order_id)->get();

        foreach ($products_order_detail as $item) {
            $product = DB::table('products')->where('id', $item->product_id)->first();

            if (!$product || $product->quantity < $item->quantity) {
                return Redirect::back()->withErrors('Sản phẩm ' . optional($product)->name . ' không đáp ứng đủ số lượng! Sản phẩm này hiện có số lượng là ' . optional($product)->quantity . '.');
            }

            DB::table('products')->where('id', $item->product_id)->decrement('quantity', $item->quantity);
        }

        DB::table('orders')->where('order_id', $order->order_id)->update(['status' => 1]);

        if($data['payment_method'] == 'vnpay'){
            $vnp_TmnCode = config('app.vnp_TmnCode');
            $vnp_HashSecret = config('app.vnp_HashSecret');
            $vnp_Url = config('app.vnp_Url');
            $vnp_Returnurl = config('app.vnp_Returnurl');

            $vnp_TxnRef = $order->order_id;
            $vnp_Amount = $data['amount'] * 100000;
            $vnp_Locale = config('app.vnp_Locale');
            $vnp_BankCode = config('app.vnp_BankCode');
            $vnp_IpAddr = $request->ip();
            $vnp_CreateDate = date('YmdHis');
            $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes', strtotime($vnp_CreateDate)));

            $inputData = [
                'vnp_Version' => '2.1.0',
                'vnp_TmnCode' => $vnp_TmnCode,
                'vnp_Amount' => $vnp_Amount,
                'vnp_Command' => 'pay',
                'vnp_CreateDate' => $vnp_CreateDate,
                'vnp_CurrCode' => 'VND',
                'vnp_IpAddr' => $vnp_IpAddr,
                'vnp_Locale' => $vnp_Locale,
                'vnp_OrderInfo' => 'Thanh toan:' . $vnp_TxnRef,
                'vnp_OrderType' => 'other',
                'vnp_ReturnUrl' => $vnp_Returnurl,
                'vnp_TxnRef' => $vnp_TxnRef,
                'vnp_ExpireDate' => $vnp_ExpireDate,
            ];

            if ($vnp_BankCode) {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            ksort($inputData);
            $query = http_build_query($inputData);
            $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
            $vnp_Url = $vnp_Url . '?' . $query . '&vnp_SecureHash=' . $vnpSecureHash;

            return redirect()->away($vnp_Url);
        }

        if ($request->email) {
            $orderOk = Order::select('orders.*', 'sizes.name as sizeName', 'colors.name as colorName')
                ->join('products', 'products.id', '=', 'orders.product_id')
                ->join('colors', 'colors.id', '=', 'products.color_id')
                ->join('sizes', 'sizes.id', '=', 'orders.size_id')
                ->where('orders.order_id', $order->order_id)->get();

            Mail::mailer('smtp')->to($request->email)->send(new MailOrderSuccess($orderOk, $request->name));
        }

        return redirect('/checkout/order-received/'.$order->order_id);
    }

    public function orderReceived($order_id){
        $order = Transaction::where('order_id', $order_id)->first();
        $order_detail = Order::where('order_id', $order_id)->where('status', 1)->get();
        if(isset($order) && isset($order_detail)){
            return view('order_received', ['success' => 'Đơn hàng của bạn đã được tiếp nhận',
                                           'order' => $order, 'order_detail' => $order_detail]);
        }
        return view('404');
    }
}
