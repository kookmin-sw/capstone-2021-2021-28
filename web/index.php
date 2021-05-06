<!DOCTYPE html>
<html style="height: -webkit-fill-available;">
<head lang="ko">
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <title></title>
</head>
<body style="height: -webkit-fill-available;">
<div class="container" style="height: -webkit-fill-available; display: flex; align-items: center; justify-content: center; flex-direction: column;">
    <form action="cam.php" method="post" style="display: contents;">
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <h3>탐지할 물체의 수</h3>
        </div>
    </div>
    <div class="row w-100 mb-5">
        <div class="col-md-12">
                <select class="form-select" aria-label="Default select example" name="total_classes">
                    <option selected>클릭하여 선택해주세요</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
        </div>
    </div>
    <div class="row w-100 mb-5">
        <div class="col-md-12">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button class="btn btn-primary" type="submit">Next</button>
            </div>
        </div>
    </div>
    </form>
</div>
</body>
</html>

