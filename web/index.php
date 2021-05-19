<!DOCTYPE html>
<html>
<?php include 'header.php'?>
<body>
<div class="container">
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

