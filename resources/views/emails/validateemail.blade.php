<!DOCTYPE html>
<html lang="fr">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>GMA500 confirmation email</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                padding:20px;
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 400;
                height: 100vh;
                margin: 0;
            }


            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
  <body>
    <h2>Bienvenu au GMA500 {{$name}},</h2>
    <p>Vous n'avez pas encore confirmé votre adresse électronique.</p>
    <p>Vous pouvez confirmer votre adresse électronique en cliquant sur le lien suivant</p>
    <a href="{{$key}}">Confirmer mon adresse électronique</a>
    <div style="margin-top:40px">
      <small>*** Ce message est généré automatiquement. Merci de ne pas y répondre.***</small>
    </div>
  </body>
</html>