<?php

// session_start();

include "./nav2.php";
if (isset($_SESSION["UserType"])) {
    if ($_SESSION["UserType"] == 'Admin') {
        echo ' <Script>window.location = "./adminDashboard.php";</script>';
    }
}

?>

<?php
$date = date('Y-m-d H:i:s');


//Setting winner and removing expired products

$sql1 = "update auction set status = 1 where eDate < '$date'";
$res1 = mysqli_query($conn, $sql1) or die(mysqli_error($conn));

$sql4 = "select * from auction where status=1 && wid = 0";
$res4 = mysqli_query($conn, $sql4) or die(mysqli_error($conn));
while ($row4 = mysqli_fetch_assoc($res4)) {
    $aaid = $row4['aid'];
    $sql3 = "SELECT * FROM bids where aid='$aaid' ORDER BY bAmount DESC LIMIT 0, 1";
    $res3 = mysqli_query($conn, $sql3) or die(mysqli_error($conn));
    while ($row3 = mysqli_fetch_assoc($res3)) {
        $uuid = $row3['uid'];
        $sql2 = "update auction set wid = '$uuid' where aid = '$aaid'";
        $res2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
    }
}

//Page

if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 0;
}


// Count products

$totalItem = 0;
$sql = "SELECT COUNT(aid) AS numbers FROM auction where status = 0;";
$res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($res)) {
    $totalItem = $row["numbers"];
}

//for next button controls
$status = 0;

if ($totalItem < 8) $page = 0;
if ($totalItem < (($page + 1) * 8)) {
    $status = 1;
}





?>

<link rel="stylesheet" href="./css/index.css">
<div class="container-fluid w-75 mx-auto mt-3 dsbd">
    <div class="bg-white rounded d-flex align-items-center justify-content-between" id="header"> <button class="btn btn-hide text-uppercase" type="button" data-toggle="collapse" data-target="#filterbar" aria-expanded="false" aria-controls="filterbar" id="filter-btn" onclick="changeBtnTxt()"> <span class="fas fa-angle-left" id="filter-angle"></span> <span id="btn-txt">Hide filters</span> </button>
        <nav class="navbar navbar-expand-lg navbar-light pl-lg-0 pl-auto"> <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mynav" aria-controls="mynav" aria-expanded="false" aria-label="Toggle navigation" onclick="chnageIcon()" id="icon"> <span class="navbar-toggler-icon"></span> </button>
            <div class="collapse navbar-collapse pl-2" id="mynav">
                <ul class="navbar-nav d-lg-flex align-items-lg-center nvd">


                    <!-- Search -->
                    <form action="index.php" method="POST">
                        <li class="nav-item d-inline-flex align-items-center justify-content-between mb-lg-0 mb-3">
                            <div class="pl-2"><input class="form-control" onkeyup="myFunction()" id="myFilter" type="text" name="search" placeholder="Search any product..."></div>
                            <button type="submit" class="btn btn-outline-secondary form-check-label active btn-secondary" name="searchh">Search</button>
                        </li>
                    </form>
                    <li class="nav-item d-lg-none d-inline-flex"> </li>
                </ul>
            </div>
        </nav>
        <div class="ml-auto mt-3 mr-2">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item"> <a href="index.php?page=<?php echo ($page - 1); ?>" class=" page-link" href="#" aria-label="Previous" <?php echo ($page == 0) ? "hidden" : ""; ?>> <span aria-hidden="true" class="font-weight-bold">&lt;</span> <span class="sr-only">Previous</span> </a> </li>
                    <!-- <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">..</a></li>
                    <li class="page-item"><a class="page-link" href="#">24</a></li> -->
                    <li class="page-item"> <a href="index.php?page=<?php echo ($page + 1); ?>" class="page-link" href="#" aria-label="Next" <?php echo ($status == 1) ? "hidden" : ""; ?>> <span aria-hidden="true" class="font-weight-bold">&gt;</span> <span class="sr-only">Next</span> </a> </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="content" class="my-4">
        <div id="filterbar" class="collapse">
            <form action="index.php" method="POST">
                <div class="box border-bottom">
                    <div class="form-group text-center">
                        <button class="btn btn-outline-primary form-check-label btn-success">Reset</button>
                        <button type="submit" class="btn btn-outline-success form-check-label active btn-success" name="filter">Filter</button>
                    </div>
                    <hr>
                    <!-- <div class="nav-item active"> <select name="sort" id="sort">
                            <option value="" hidden selected>Sort by</option>
                            <option value="price">Price Asc.</option>
                            <option value="popularity">Price Dsc.</option>
                            <option value="rating">Featured</option>
                        </select>
                    </div> -->



                </div>
                <div class="box border-bottom">
                    <div class="box-label text-uppercase d-flex align-items-center">Categories <button class="btn ml-auto" type="button" data-toggle="collapse" data-target="#inner-box" aria-expanded="false" aria-controls="inner-box" id="out" onclick="outerFilter()"> <span class="fas fa-plus"></span> </button> </div>
                    <div id="inner-box" class="collapse mt-2 mr-1">
                        <?php
                        $sql = "select * from category";
                        $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                        while ($row = mysqli_fetch_assoc($res)) { ?>
                            <div class="my-1 mb-2"> <label class="tick"><?php echo $row['cName']; ?><input type="checkbox" name="<?php echo $row['cid']; ?>"><span class="check"></span> </label> </div>
                        <?php } ?>

                    </div>
                </div>

            </form>
        </div>
        <div id="myItems">
            <div class="row mx-0">
                <?php


                $sql1 = "select * from auction where status = 0 && eDate > '{$date}'";
                if (isset($_POST['filter'])) {
                    $sql = "select * from category";
                    $res = mysqli_query($conn, $sql) or die(mysqli_error($conn));
                    $first = 0;
                    while ($row = mysqli_fetch_assoc($res)) {
                        if (isset($_POST[$row['cid']])) {
                            if ($first == 0) {
                                $sql1 = $sql1 . " && cid = {$row['cid']}";
                                $first = 1;
                            } else  $sql1 = $sql1 . " || cid = {$row['cid']}";
                        }
                    }
                }
                if (isset($_POST['searchh'])) {
                    $search = $_POST["search"];
                    $sql1 = "SELECT * FROM auction WHERE auction.title LIKE '%$search%'";
                }




                // echo $sql1;
                $res1 = mysqli_query($conn, $sql1 . " order by views desc limit " . $page * 8 . ",8") or die(mysqli_error($conn));
                while ($row = mysqli_fetch_assoc($res1)) { ?>
                    <div class="show col-lg-3 col-md-6">

                        <div class="card d-flex flex-column align-items-center">
                            <div class="product-name px-2">
                                <?php echo (strlen($row['title']) > 45) ? substr($row['title'], 0, 45) . '...' : $row['title']; ?>
                            </div>
                            <div class="card-img">
                                <div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php
                                        $images = $row['pictures'];
                                        $image = explode(": ", $images);
                                        $active = true;
                                        foreach ($image as $img) { ?>
                                            <div class="carousel-item <?php echo ($active == true) ? "active" : ""; ?>">
                                                <img class="d-block w-100" src="./product/<?php echo $img; ?>" alt=<?php echo $img; ?>>
                                            </div>
                                        <?php
                                            $active = false;
                                        } ?>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body p-0 my-0">
                                <div class="text-muted text-center my-0 pb-0 pt-2">Highest Bid:
                                    <?php
                                    $aaid = $row['aid'];

                                    $lprice = $row['iPrice'];
                                    $sql2 = "select MAX(bAmount) from bids where aid = '$aaid'";
                                    $res2 = mysqli_query($conn, $sql2) or die(mysqli_error($conn));
                                    if (mysqli_num_rows($res2) > 0) {
                                        while ($row2 = mysqli_fetch_assoc($res2)) {
                                            $lprice = (int) $row2['MAX(bAmount)'];
                                            $lprice = ($lprice > $row['iPrice']) ? $lprice : $row['iPrice'];
                                        }
                                    }
                                    echo $lprice;

                                    ?>
                                    <hr class="my-1 py-0">
                                </div>
                                <div class="text-muted py-0 desc">

                                    <?php echo (strlen($row['description']) > 110) ? substr($row['description'], 0, 110) . '...' : $row['description']; ?>
                                </div>
                                <hr class="my-1 py-0">
                                <div class="text-muted py-0"><?php echo $row['district'] . ', ' . $row['location']; ?></div>
                            </div>
                            <a href="./product.php?id=<?php echo $row['aid']; ?>" class="btn align-bottom btn-primary-outline stretched-link"></a>


                        </div>

                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

</body>

</html>

<script>
    // function myFunction() {
    //     var input, filter, cards, cardContainer, h5, title, i;
    //     input = document.getElementById("myFilter");
    //     filter = input.value.toUpperCase();
    //     cardContainer = document.getElementById("myItems");
    //     cards = cardContainer.getElementsByClassName("show");
    //     for (i = 0; i < cards.length; i++) {
    //         title = cards[i].querySelector(".product-name");
    //         if (title.innerText.toUpperCase().indexOf(filter) > -1) {
    //             cards[i].style.display = "";

    //         } else {
    //             cards[i].style.display = "none";
    //         }
    //     }
    // }

    var filterBtn = document.getElementById('filter-btn');
    var btnTxt = document.getElementById('btn-txt');
    var filterAngle = document.getElementById('filter-angle');

    document.getElementById('filterbar').style.display = "block";
    var count = 0,
        count2 = 0;

    function changeBtnTxt() {
        count++;
        if (count % 2 != 0) {
            document.getElementById('filterbar').style.display = "none";
            filterAngle.classList.add("fa-angle-right");
            btnTxt.innerText = "show filters"
            filterBtn.style.backgroundColor = "#36a31b";
        } else {
            document.getElementById('filterbar').style.display = "block";
            filterAngle.classList.remove("fa-angle-right")
            btnTxt.innerText = "hide filters"
            filterBtn.style.backgroundColor = "#ff935d";
        }

    }
    document.getElementById('inner-box').style.display = "block";

    function outerFilter() {
        let now = document.getElementById('inner-box');
        if (now.style.display != "none") {
            now.style.display = "none";
        } else {
            now.style.display = "block"
        }

    }
</script>