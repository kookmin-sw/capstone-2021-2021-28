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
                <a class="btn btn-warning" data-bs-toggle="modal" href="#exampleModalToggle" role="button">도움말</a>
                &nbsp;
                <button class="btn btn-primary" type="submit">Next</button>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalToggleLabel">Object Detection</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/class_num1.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">다음으로</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalToggleLabel2">Object Detection</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <image src="direction/class_num2.png" style="width:100%; height:100%; border:none"></image>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">이전으로</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
</body>
</html>
