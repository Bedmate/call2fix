<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
    {!! $head ?? '' !!}
</head>

<body>

    <div>
        <table border="0" cellspacing="0" cellpadding="0" style="font-size:14px;max-width:600px;margin:auto;background-color:white" width="100%">
            <tbody>
                <tr>
                    <td align="center">
                        <table border="0" cellspacing="0" cellpadding="0" style="font-size:14px" width="100%">
                            <tbody>
                                <tr>
                                    <td style="padding:20px 10px 35px">
                                        <table width="100%">
                                            <tbody>
                                                <tr style="height:25px">
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td align="center">
                                                        <img style="width: 139px; margin: 0px auto;" src="https://alphamead.lon1.digitaloceanspaces.com/3691b3e5-68ae-4e2c-95a7-bdafc43cd378/1748694060_f599b5b3-4f46-48b9-8e23-9ce2d4504529.png" alt="Call2Fix Logo">
                                                    </td>
                                                </tr>
                                                <tr style="height:25px">
                                                    <td>&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="border-top:1px solid rgba(0,0,0,0.08)"></td>
                                                </tr>
                                                <tr style="height:30px">
                                                    <td>&nbsp;</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        {{-- Dynamic Email Content --}}
                                        {!! $slot !!}

                                    </td>
                                </tr>
                                <tr style="height:22px">
                                    <td>&nbsp;</td>
                                </tr>
                                <tr style="height:22px">
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px dashed rgba(0,0,0,0.08)"></td>
                                </tr>
                                <tr style="height:22px">
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        If you have any questions or need assistance, our support team is here to help. Simply reply to this email or call us at
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Phone : <strong>07015300138</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Email : <a href="mailto:call2fixteam@alphamead.com" target="_blank" style="text-decoration:underline">call2fixteam@alphamead.com</a>
                                    </td>
                                </tr>
                                <tr style="height:32px">
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        Best regards,
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        The Call2Fix Team
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr style="height:50px">
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding:24px 20px;font-size:14px;line-height:18px;background:rgb(0,184,118);color:white">
                        Alexander House, Otunba Jobi Fele way, Ikeja, Lagos
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:30px 0px">
                        <a href="FB: https://web.facebook.com/profile.php?id=100043562603844" target="_blank"><img style="width: 32px; height: 32px;" src="https://files.opayweb.com/images/marketing/activityTemplate/2022-12-05/picf_809.png" alt=""></a>
                        <a href="https://x.com/AlphaMeadGroup" target="_blank"><img style="width: 32px;" src="https://files.opayweb.com/images/marketing/activityTemplate/2022-12-05/pict_220.png" alt=""></a>
                        <a href="https://instagram.com/call2fix_ng" target="_blank"><img style="width: 32px;" src="https://files.opayweb.com/images/marketing/activityTemplate/2022-12-05/picin_598.png" alt=""></a>
                        <a href="https://linkedin.com/showcase/call2fix./" target="_blank"><img style="width: 32px;" src="https://files.opayweb.com/images/marketing/activityTemplate/2022-12-05/picn_618.png" alt=""></a>
                        <a href="mailto:call2fixteam@alphamead.com" target="_blank"><img style="width: 32px;" src="https://files.opayweb.com/images/marketing/activityTemplate/2022-12-05/pice_466.png" alt=""></a>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:20px;background:rgb(244,249,253);font-size:12px;line-height:20px;text-align:center;color:rgba(0,0,0,0.45)">
                        <p>Copyright Ⓒ {{ date('Y') }} Alpha Mead Facilities – RC 643946</p>
                        <p>Call2Fix is a digital platform operated by Alpha Mead Digital Solutions. Call2Fix provides facility management and maintenance services through verified service providers. All service providers are vetted, and transactions are secured for your safety and convenience.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


</body>

</html>