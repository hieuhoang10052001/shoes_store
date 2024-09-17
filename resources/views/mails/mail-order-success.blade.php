<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thông báo đơn hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <p>Chào {{$name}},</p>
    <p>Đơn hàng của bạn đã được đặt thành công</p>
    <p>Chi tiết đơn hàng</p>
    <br/>

    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Mã sản phẩm</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Số lượng</th>
                <th>Thành tiền (VNĐ)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>{{$item->code}}</td>
                    <td>{{$item->colorName}}</td>
                    <td>{{$item->sizeName}}</td>
                    <td>{{$item->quantity}}</td>
                    <td>{{number_format($item->price_sale*$item->quantity*1000 ,0 ,'.' ,'.')}}</td>
                </tr>
            @endforeach
        </tbody>
    </table> 

    <p>Bạn có thể biết thêm chi tiết về đơn đặt hàng của mình bằng cách đăng nhập vào trang web của chúng tôi.</p>
    
    <p>Cảm ơn bạn một lần nữa vì đã chọn chúng tôi.</p>

    <p>Regards,</p>
    <br>
    <p>HQ Store</p>
</body>
</html>
