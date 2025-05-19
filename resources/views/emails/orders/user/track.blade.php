<!DOCTYPE html>
<html>

<head>
    <title>Order Placed</title>
</head>

<body>

    <table style="width:600px; margin:0 auto; background-color:#f5f5f5; border-collapse: collapse; font-family: sans-serif;">
        <tr>
            <td style=" background-color:#f8f8f8; color: #0d6efd; text-align:center; padding: 15px 15px 11px;" colspan="2"><img
                    style="width: 100px;" src="https://medi-expresss.b-cdn.net/emailTemplate%20/logo.png" /> </td>
        </tr>

        <tr>
            <td style="background-color: #4d93e8; text-align:center; padding: 20px 15px;"
                colspan="2">
                <h4 style="margin: 0; font-size: 20px;color:#fff;">TRACK YOUR ORDER</h4>
            </td>
        </tr>

        <tr>
            <td style="text-align:center; padding: 0px 0px 0px 0px;"><img
                    style="width: 100%;" src="track-order-email.jpg" /> </td>
        </tr>

        <tr>
            <td style="background-color: #fcfcfc; text-align:left; padding: 7px 15px 7px 15px; font-size: 14px; border-bottom: 2px solid #f1f1f1;">
                <p><strong>Tracking ID:</strong> {{ $data['tracking_number'] }}</p>
            </td>
        </tr>
		
		<tr>
            <td style="background-color: #fcfcfc; text-align:left; padding: 7px 15px 7px 15px; font-size: 14px; border-bottom: 2px solid #f1f1f1;">
                <p><strong>Tracking Link:</strong> {{ $data['logistics_partner_name'] }} &nbsp;  {{ $data['logistics_partner_link'] }}</p>
            </td>
        </tr>
		
		<tr>
            <td style="background-color: #fcfcfc; text-align:left; padding: 0px 15px 20px 15px; font-size: 14px;">
                <p><strong>Shipping Address:</strong></p>
				<p>{{$data['address']['address_line2']}} ,{{$data['address']['address_line1']}}<br />
                    {{$data['address']['city']}} , {{$data['address']['state']}} , {{$data['address']['country']}}<br />
                    < {{$data['address']['postal_code']}}> <br />
                        
                </p>
				<p><strong>Phone Number:</strong> {{ $data['address']['mobile'] }}</p>
            </td>
        </tr>


        <tr>
            <td style="text-align:center; padding: 20px 15px; background-color:#4d93e8; color:#fff; font-size: 14px; line-height: 23px;">
                <p style="margin:0px;">Copyright © 2024 MediExpress. All Rights Reserved.</p>
            </td>
        </tr>



    </table>

</body>

</html>