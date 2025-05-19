<!DOCTYPE html>
<html>

<head>
    <title>Order Placed</title>
</head>

<body>

    <table
        style="width:600px; margin:0 auto; background-color:#f8f8f8; border-collapse: collapse; font-family: sans-serif;">
        <tr>
            <td style="color: #0d6efd; text-align:center; padding: 15px 15px 11px;" colspan="2"><img
                    style="width: 100px;" src="https://medi-expresss.b-cdn.net/emailTemplate%20/logo.png" /> </td>
        </tr>

        <tr>
            <td style="background-color: #4d93e8; text-align:center; padding: 20px 15px; line-height: 23px;"
                colspan="2">
                <h4 style="margin: 0; font-size: 20px;color:#fff;">ORDER PLACED</h4>
            </td>
        </tr>

        <tr>
            <td style="text-align:center; padding: 15px 15px 0px 15px; line-height: 23px;" colspan="2"><img
                    style="width: 100px;" src="https://medi-expresss.b-cdn.net/emailTemplate%20/happy.png" /> </td>
        </tr>

        <tr>
            <td style="text-align:center; padding: 0px 15px 18px 15px; line-height: 23px;" colspan="2">
                <h4 style="margin: 0; font-size: 20px; color: #4d93e8;">
                    {{$data['order']['user']['first_name']}} Placed an
                    Order!
                </h4>
            </td>
        </tr>

        <tr>
            <td style="text-align:left; padding: 15px; line-height: 23px;">
                <h4 style="margin: 0; font-size: 18px;">Shipping Address</h4>
            </td>
            <td style="text-align:left; padding: 15px; line-height: 23px;">
                <h4 style="margin: 0; font-size: 18px;">Order Summary</h4>
            </td>
        </tr>

        <tr>
            <td
                style="text-align:left; padding: 0px 15px 20px 15px; font-size: 14px; line-height: 23px; vertical-align: text-top; width: 55%;">
                <p>{{$data['address']['address_line2']}} ,{{$data['address']['address_line1']}}<br />
                    {{$data['address']['city']}} , {{$data['address']['state']}} , {{$data['address']['country']}}<br />
                    < {{$data['address']['postal_code']}}> <br />
                        {{ $data['address']['mobile']}} <br />
                </p>

                @foreach($data['product'] as $value)

                <img style="width: 150px;"
                    src="{{ 'https://medi-expresss.b-cdn.net/images/' .$value['featured_img'] }}" />
                @endforeach
            </td>
            <td style="text-align:left; padding: 0px 15px 20px 15px; font-size: 14px; line-height: 23px; width: 45%;">
                <p>
                    <strong>Order No:</strong> {{ $data['order']['order_number']}}
                    <br />
                    <strong>Order Date:</strong>{{$data['order']['created_at']}}
                </p>

                <p>
                    <strong>Sub Total:</strong> {{$data['order']['sub_total']}}
                    <br />
                    </strong>@isset($data['order']['shipping_charges'] )<strong>Shipping Charges:
                        {{$data['order']['shipping_charges']}} <br />@endisset
                    </strong>@isset($data['order']['discounted_amount'] )<strong>Coupon:
                        {{$data['order']['discounted_amount']}} <br />@endisset
                        <br />
                        <strong>Total:</strong> {{ $data['order']['currency']}} {{$data['order']['total_amount']}}
                </p>

                <p>
                    @foreach($data['product'] as $value)
                    <strong>Product Name:</strong> {!! $value['name'] !!}<br />

                    <strong>Quantity:</strong>2<br />

                    <strong>Pack Of:</strong> {{$value['variation']['pack_of']}}<br />

                    <strong>Price:</strong> {{$value['variation']['actual_price']}}<br /><br /><br />


                    @endforeach
                </p>
            </td>
        </tr>


        <tr>
            <td style="text-align:center; padding: 20px 15px; background-color:#4d93e8; color:#fff; font-size: 14px; line-height: 23px;"
                colspan="2">
                <p style="margin:0px;">Copyright © 2024 MediExpress. All Rights Reserved.</p>
            </td>
        </tr>



    </table>

</body>

</html>