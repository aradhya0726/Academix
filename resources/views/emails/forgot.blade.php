<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>Reset Your Password</title>
<!--[if mso]>
<noscript>
<xml>
<o:OfficeDocumentSettings>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
</noscript>
<![endif]-->
<style>
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    body { margin: 0; padding: 0; width: 100% !important; height: 100% !important; background-color: #F3F5F9; }
    a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }

    @media only screen and (max-width: 600px) {
        .email-container { width: 100% !important; }
        .fluid-padding { padding-left: 24px !important; padding-right: 24px !important; }
        .stack-title { font-size: 26px !important; }
    }
</style>
</head>
<body style="margin:0; padding:0; background-color:#F3F5F9;">

<div style="display:none; font-size:1px; color:#F3F5F9; line-height:1px; max-height:0; max-width:0; opacity:0; overflow:hidden;">
    Reset your Student Attendance &amp; Academic Record Management System password. This link expires in 60 minutes.
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F3F5F9;">
<tr>
<td align="center" style="padding: 40px 16px;">

    <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px; max-width:600px;">

        <tr>
            <td align="center" style="padding-bottom: 24px;">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="width:40px; height:40px; border-radius:50%; border:1px solid #4F46E5; text-align:center; vertical-align:middle; font-family:Georgia, 'Times New Roman', serif; font-weight:700; font-size:13px; color:#4F46E5; letter-spacing:0.5px;">
                            LTU
                        </td>
                        <td style="padding-left:10px; font-family:Georgia, 'Times New Roman', serif; font-weight:600; font-size:15px; color:#101828;">
                            Student Attendance &amp; Records
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="background-color:#FFFFFF; border:1px solid #E6E9F0; border-top:3px solid #4F46E5; border-radius:16px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="fluid-padding" style="padding: 44px 48px 12px 48px;">
                            <p style="margin:0 0 6px 0; font-family:Arial, Helvetica, sans-serif; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:#4F46E5;">
                                Secure University Portal
                            </p>
                            <h1 class="stack-title" style="margin:0 0 16px 0; font-family:Georgia, 'Times New Roman', serif; font-weight:500; font-size:28px; line-height:1.25; color:#101828;">
                                Reset your password
                            </h1>
                            <p style="margin:0 0 8px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:1.7; color:#667085;">
                                Hi {{ $user->name ?? 'there' }},
                            </p>
                            <p style="margin:0 0 28px 0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:1.7; color:#667085;">
                                We received a request to reset the password for your account
                                (<strong style="color:#344054;">{{ $user->email }}</strong>).
                                Click the button below to choose a new password. This link is single-use and will expire in 60 minutes.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="fluid-padding" align="center" style="padding: 0 48px 32px 48px;">
                            <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $resetUrl }}" style="height:50px;v-text-anchor:middle;width:260px;" arcsize="16%" fillcolor="#4F46E5" stroke="f">
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-family:Arial,sans-serif;font-size:15px;font-weight:bold;">Reset Password</center>
                            </v:roundrect>
                            <![endif]-->
                            <!--[if !mso]><!-- -->
                            <a href="{{ $resetUrl }}"
                               style="display:inline-block; background-color:#4F46E5; color:#FFFFFF; font-family:Arial, Helvetica, sans-serif; font-size:15px; font-weight:700; letter-spacing:0.2px; text-decoration:none; padding:15px 40px; border-radius:10px;">
                                Reset Password
                            </a>
                            <!--<![endif]-->
                        </td>
                    </tr>

                    <tr>
                        <td class="fluid-padding" style="padding: 0 48px 36px 48px; border-top:1px solid #E6E9F0;">
                            <p style="margin:24px 0 6px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#98A2B3;">
                                If the button above doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:12px; word-break:break-all;">
                                <a href="{{ $resetUrl }}" style="color:#4F46E5; text-decoration:underline;">{{ $resetUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="fluid-padding" style="padding: 0 48px 40px 48px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F3F5F9; border-radius:10px;">
                                <tr>
                                    <td style="padding:14px 18px; font-family:Arial, Helvetica, sans-serif; font-size:12.5px; line-height:1.6; color:#667085;">
                                        Didn't request this? You can safely ignore this email — your password won't be changed until you click the link above and set a new one.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 28px 24px 0 24px;">
                <p style="margin:0 0 4px 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#98A2B3;">
                    &copy; {{ date('Y') }} Student Attendance &amp; Academic Record Management System
                </p>
                <p style="margin:0; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#98A2B3;">
                    Lumbini Technological University
                </p>
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>