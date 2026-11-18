
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to LTU Academics</title>
</head>

<body style="margin:0; padding:0; background:#f4f5f9;
             font-family:Arial,sans-serif;">

    <div style="max-width:600px; margin:40px auto;
                background:#ffffff; border-radius:12px;
                overflow:hidden;">

        <div style="background:#252b55; padding:30px;
                    text-align:center; color:#ffffff;">

            <h1 style="margin:0; font-size:25px;">
                LTU Academics
            </h1>

            <p style="margin:10px 0 0; color:#d5d9f2;">
                Student Attendance & Academic Records
            </p>
        </div>

        <div style="padding:35px 30px; color:#333333;">

            <h2 style="color:#252b55;">
                Welcome, {{ $user->name }}!
            </h2>

            <p>
                Your account has been successfully created
                on the LTU Academics platform.
            </p>

            <p>
                You can now access your account using the
                following details:
            </p>

            <div style="background:#f4f5f9; padding:20px;
                        border-radius:8px; margin:25px 0;">

                <p>
                    <strong>Name:</strong>
                    {{ $user->name }}
                </p>

                <p>
                    <strong>Email:</strong>
                    {{ $user->email }}
                </p>

                <p>
                    <strong>Account Type:</strong>
                    {{ ucfirst($user->role) }}
                </p>

                <p style="margin-bottom:0;">
                    <strong>Password:</strong>
                    <span style="color:#4f46e5;
                                 font-weight:bold;
                                 word-break:break-all;">
                        {{ $password }}
                    </span>
                </p>

            </div>

            <p>
                Click the button below to access your account.
            </p>

            <div style="text-align:center; margin:30px 0;">

                <a href="{{ $loginUrl }}"
                   style="background:#4f46e5;
                          color:#ffffff;
                          padding:14px 30px;
                          text-decoration:none;
                          border-radius:7px;
                          display:inline-block;
                          font-weight:bold;">

                    Login to Your Account

                </a>

            </div>

            <p style="font-size:14px; color:#666666;">
                For your security, please change your password
                after your first login. Do not share your
                password with anyone.
            </p>

            <p>
                If you have any questions, please contact
                your system administrator.
            </p>

            <p>
                Best regards,<br>
                <strong>LTU Academics Team</strong>
            </p>

        </div>

        <div style="background:#f4f5f9; padding:18px;
                    text-align:center; color:#888888;
                    font-size:12px;">

            © {{ date('Y') }} LTU Academics.
            All rights reserved.

        </div>

    </div>

</body>
</html>
