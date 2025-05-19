<section style="padding: 5% 10% 5% 10%;">
    <img src="https://medi-expresss.b-cdn.net/email/logo.png" />
    <div style="padding:3% 0 0 0;">
        <h1 style="font-family:'Jost'; font-size: 24px; font-weight:700; line-height: 33.6px;">Forget Password</h1>
    </div>
    <div>
        <p style="font-family:'Jost'; font-size: 16px; font-weight:400; line-height: 24px; letter-spacing: 0.2px;">Hi! {{ ucwords($user['name']) }}</p>
        <p style="font-family:'Jost'; font-size: 16px; font-weight:400; line-height: 24px; letter-spacing: 0.2px; margin-bottom: 3%;">A request has been received to change the password of your <br />Mediexpress account</p>
        <a href="{{ url('reset-password', $user['token']) }}" style="background-color: #3db2ff;color: white; border: none; display: inline-flex; padding: 1rem 2rem;text-align: center;">Reset Password </a>
        <p style="font-family:'Jost'; font-size: 16px; font-weight:400; line-height: 24px; letter-spacing: 0.2px;">If you don't initiate this request please contact us immediately at <br /> mediexpress@help.com</p>
    </div>
</section>