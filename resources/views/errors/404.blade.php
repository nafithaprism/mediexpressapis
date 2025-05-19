<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="icon" href="favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#000000" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="apple-touch-icon" href="logo192.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <title>404 Page Not Found - Royal Spirit</title>
    <!-- font import  -->
    <link
      fetchpriority="high"
      rel="preload"
      media="print"
      href="https://fonts.googleapis.com/css2?family=Playfair:ital,opsz,wght@0,5..1200,400;0,5..1200,500;0,5..1200,600;1,5..1200,400;1,5..1200,500;1,5..1200,600&family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
      onload="this.onload=null;this.removeAttribute('media');"
    />
    <!-- Bootstrap CSS -->
    <link
      rel="preload"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.1/css/bootstrap.min.css"
    />
    <style>
      body {
        font-family: "Montserrat", sans-serif;
      }
      /* ===================== 404 ERROR ======================*/
      .error-page-inn {
        margin-top: 1rem;
      }
      .error-page-inn h1 {
        font-size: 160px;
        font-weight: 800;
        color: #000;
        text-align: center;
        font-family: "Playfair", sans-serif;
      }

      .error-page-inn h1 span {
        display: inline-block;
        font-family: "Playfair", sans-serif;
      }

      .error-page-inn h1 .zero {
        color: #821a1a;
      }

      .four {
        animation: bounce-down 2s infinite;
      }

      .zero {
        animation: bounce-up 2s infinite;
      }

      .error-page-inn h2 {
        font-family: "Playfair", sans-serif;
        font-weight: 600;
      }

      .home-btn {
        background: #821a1a;
        color: #fff;
        border-color: #821a1a;
        text-decoration: none;
        padding: 0.7rem 4rem;
        transition: all 0.5s;
      }
      .home-btn:hover {
        background: transparent;
        outline: 1px solid #821a1a;
        color: #821a1a;
      }

      /* Bounce Animations */
      @keyframes bounce-up {
        0%,
        20%,
        50%,
        80%,
        100% {
          transform: translateY(0);
        }
        40% {
          transform: translateY(-30px);
        }
        60% {
          transform: translateY(-15px);
        }
      }

      @keyframes bounce-down {
        0%,
        20%,
        50%,
        80%,
        100% {
          transform: translateY(0);
        }
        40% {
          transform: translateY(30px);
        }
        60% {
          transform: translateY(15px);
        }
      }

      /*Responsive*/
      @media (max-width: 767px) {
        .error-page-inn h1 {
          font-size: 76px;
        }
      }
      @media (min-width: 768px) and (max-width: 991px) {
        .error-page-inn h1 {
          font-size: 100px;
        }
      }
    </style>
  </head>
  <body>
    <div class="error-page-inn">
      <div class="container">
        <header>
          <img src="logo.png" alt="Royal Spirit Logo" width="95px" />
        </header>
        <div
          class="d-flex flex-column justify-content-center align-items-center mb-5"
        >
          <h1>
            <span class="four">4</span>
            <span class="zero">0</span>
            <span class="four">4</span>
          </h1>
          <h2 class="text-center">Oops, something went wrong!</h2>
          <p class="text-center">
            The page you are looking for was moved, removed, renamed or might
            never have existed.
          </p>
          <a href="/" class="home-btn"> Go To Home </a>
        </div>
      </div>
    </div>
  </body>
</html>
