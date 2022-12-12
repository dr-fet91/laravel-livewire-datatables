<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DataTables</title>
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">


<style>
    th{
        position: relative !important;
        cursor: pointer;
    }
    th::after, th::before{
        position: absolute;
        display: block;
        opacity: .125;
        right: 10px;
        line-height: 12px;
        font-size: .8em;
    }
    th::before{
        content: "▲";
        bottom: 50%;
    }
    th::after{
        content: "▼";
        top: 50%;  
    }
    .after-on::after, .before-on::before{
        opacity: 1;
    }
</style>


</head>
<body>
    
    <div class="container-fluid">
        <div class="row mt-5">
            <h1 class="d-block text-center">Users with their car</h1>
            <livewire:user-cars-tbl tableName="myTbl">
            {{-- <livewire:user-cars-tbl tableName="x"> --}}
        </div>
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
