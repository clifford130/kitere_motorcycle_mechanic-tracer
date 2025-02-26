<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:../loginup/index.php');
}

include '..//loginup/db_connect.php'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/search.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         /* Navigation Bar Styles */
      nav {
         background: #191919;
         padding: 10px 20px;
         display: flex;
         justify-content: space-between;
         align-items: center;
      }
      nav .logo {
         color: #2ecc71;
         font-size: 24px;
         font-weight: bold;
         text-decoration: none;
      }
      nav ul {
         list-style: none;
         margin: 0;
         padding: 0;
         display: flex;
         align-items: center;
      }
      nav ul li {
         margin-left: 20px;
         position: relative;
      }
      nav ul li a {
         color: #fff;
         text-decoration: none;
         font-size: 16px;
      }
      nav ul li a:hover {
         color: #2ecc71;
      }
      /* Dropdown Styles */
      nav ul li .dropdown {
         display: none;
         position: absolute;
         top: 30px;
         right: 0;
         background: #191919;
         border: 1px solid #2ecc71;
         border-radius: 4px;
         min-width: 120px;
         z-index: 1000;
      }
      nav ul li:hover .dropdown {
         display: block;
      }
      nav ul li .dropdown a {
         display: block;
         padding: 10px;
         color: #fff;
         text-decoration: none;
      }
      nav ul li .dropdown a:hover {
         background: #2ecc71;
      }
      body { font-family: sans-serif; background: #34495e; color: #fff; }
      .container { width: 90%; margin: 20px auto; }
      table { width: 100%; border-collapse: collapse; }
      th, td { border: 1px solid #2ecc71; padding: 10px; text-align: center; }
      th { background: #191919; }
      a { color: #2ecc71; text-decoration: none; }

           :root {
            --background-color: #fff; 
            --text-color: #000; 
        }
/* 
        body {
            background-color: var(--background-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }
        body.dark {
            --background-color: #333;
            --text-color: #fff;
        } */
        * {
            margin: 0;
            padding: 0;
        }

        body {
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
        }

        .user {
            text-align: right;
        }

        .search {
            text-align: center;
            margin-top: 0px;
        }

        .search button {
            border: none;
            background: white;
            cursor: pointer;
        }

        .search::placeholder {
            font-size: larger;
        }

        input {
            padding-left: 50px;
            padding-right: 10px;
            padding-top: 1.5px;
            padding-bottom: 1.5px;
        }

          .logout{
            padding-left: 10px;
            padding-right: 10px;
            font-size: large;
            background-color: blue;
            border-radius: 10px;
        }

        .mechanic-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            background-color:rgb(95, 89, 89);
            width: 500px;;
            
        }

        .mechanic-card h3 {
            margin-bottom: 10px;
        }

        .mechanic-card p {
            margin-bottom: 5px;
        }
        button:hover{
            cursor:pointer;

        }
        .profile-pic {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    overflow: hidden;
    /
    transition: transform 0.3s ease; 
    cursor: pointer;
}

/* Hover effect */
.profile-pic:hover {
    transform: scale(1.05); 
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
}
        /* Loader Styles */
#loader {
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transition: opacity 1s ease; /* Smooth fade out */
  }
  .spinner {
    border: 8px solid #f3f3f3;
    border-top: 8px solid #2ecc71;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
  }
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  /* Initially hide main content */
  #main-content {
    display: none;
  }
    </style>
</head>
<body>
    <div id="loader">
        <div class="spinner"></div>
      </div>
    
      <!-- Main Content -->
      <div id="main-content">
    
   
<!-- Navigation Bar -->
<nav>
    <a href="./main.php" class="logo">KITERE MOTORCYCLE MECHANIC TRACER</a>
    <ul>
       <li><a href="main.php">Homepage</a></li>
       <li><a href="..//loginup/booking.php">Book mechanic</a></li>
       
    </ul>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
     
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
        <ul class="navbar-nav">
          <!-- <li class="nav-item"><a class="nav-link" href="mechanic_dashboard.php">Dashboard</a></li> -->
          <!-- <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li> -->
           
    <div class="search">
        <input type="search" id="live-search" placeholder="Search a mechanic">
        <button id="search-button">
            <!-- <i class='bx bx-search'></i> -->
    </button>
    </div>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown">
              <img src="../img/profile.jpeg" alt=""class="profile-pic" style="color:white;">
              <span style="color: white;">Profile</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              
               
                <?php echo $_SESSION['email']; 
            ?>
            
              
              <li><a class="dropdown-item" href="..//loginup/logout.php"style="color:green;">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</nav>

    

    <!-- search -->
    <div id="searchresult"></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            //handles search input
            function search() {
                var input = $("#live-search").val();
                if (input !== "") {
                    $.ajax({
                        url: "./livesearch.php",
                        method: "POST",
                        data: { input: input },
                        success: function(data) {
                            $("#searchresult").html(data);
                        }
                    });
                } else {
                // Clears search resultswhen no input
                    $("#searchresult").html(""); 
                }
            }

            // Call search function when key is released in the search input
            $("#live-search").keyup(function() {
                search();
            });

            $("#search-button").click(function(e) {
                e.preventDefault();
                search();
            });
        });
    </script>
    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark'); 
            const isDarkMode = document.body.classList.contains('dark');
            localStorage.setItem('darkMode', isDarkMode);
        }

        const isDarkMode = localStorage.getItem('darkMode') === 'true';

        if (isDarkMode) {
            document.body.classList.add('dark');
        } else {
            document.body.classList.add('light');
        }

        window.onload = function() {
      var loader = document.getElementById('loader');
      // Fade out the loader
      loader.style.opacity = 0;
      // After the transition completes (1s), hide the loader and show main content
      setTimeout(function() {
        loader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 1000); // Duration matches the CSS transition time
    };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
