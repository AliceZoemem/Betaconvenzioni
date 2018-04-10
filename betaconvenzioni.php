<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <script src="js/jquery-3.3.1.min.js"></script> 
    <script src="js/bootstrap/bootstrap.min.js"></script> 
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css" />
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&language=it&key=AIzaSyAJcEn33O5ntSQ8p-tJ3n7Ies5L9-0HO38"></script>
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>
    

    <style>

    /* ~ ~ Navigation ~ ~ */
    .logo-img{
        max-width:80%;
        max-height:100%;
    }

    li.nav-item {
        border: 1px solid transparent !important;
        text-align:center;
        transition:0.4s;
        min-width:7vw;
    }

    li.nav-item:hover {
        border: 1px solid #fc7b00 !important;
        color: #000000;
        background-color: #ffffff; 
        cursor:pointer;
    }

    a.active {
        border: 1px solid #fc7b00;
        box-shadow:inset 0 3px 8px rgba(0, 0, 0, 0.125);
    } 

    /* ~ ~ End Navigation ~ ~ */
        

    /* ~ ~ Page Content ~ ~ */

    .head-separator{
        height:30px;
    }

    /* ~ ~ End Page Content ~ ~ */

        
    </style>
</head>
<body> 
    


    <!-- ~ ~ Navigation Bar ~ ~ -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" style="padding:0 15px;">
            <img src="img/logo.png" class="logo-img" />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="background-color:#fc7b00;">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a id="link-home" class="nav-link active" onclick="window.location.search='homepage'">Home</a>
                </li>
                <li class="nav-item">
                    <a id="link-profile" class="nav-link" onclick="window.location.search='profile'">Profilo</a>
                </li>
                <li class="nav-item">
                    <a id="link-logout" class="nav-link" onclick="window.location.search='logout'">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="head-separator" ></div>

    <!-- ~ ~ Page Content ~ ~ -->
    <div id="page-content"></div>


    <script>

        $(document).ready(function () {

            $('#link-home').removeClass('active');
            $('#link-profile').removeClass('active');
            $('#link-logout').removeClass('active');

            
            if(window.location.search == "?profile"){
                $("#page-content").load('profile.php', function () {
                    $('#link-profile').addClass('active');

                });
            }
            else if(window.location.search == "?logout"){
                window.location.href = 'logout.php';
            }
            else {
                $("#page-content").load('homepage.php', function() {
                    $('#link-home').addClass('active');

                    tinymce.init({ selector:'#txtDescrizione' });
                });
            }
        });

    </script>
    
</body>
</html>






















