<div class="dxl dxl-admin-members members-list-container">
    <div class="header">
        <h1 class="headline">Medlemmer</h1>
        <div class="actions">
            <button class="button-primary export-members">Eksporter medlemmer</button>
        </div>
    </div>
    
    <div class="content mt-6">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2 col-xxl-2">
                <!-- Menu tabs start -->
                <div class="d-flex align-items-start flex-col flex-sm-col flex-md-col">
                    <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-lan-tab" data-bs-toggle="pill" data-bs-target="#v-pills-payed" type="button" role="tab" aria-controls="v-pills-lan" aria-selected="true">Betalte medlemmer</button>
                        <button class="nav-link" id="v-pills-online-tab" data-bs-toggle="pill" data-bs-target="#v-pills-pending" type="button" role="tab" aria-controls="v-pills-online" aria-selected="false">Afventende medlemmer</button>
                    </div>
                </div> <!-- Menu tabs end -->
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-10 col-xl-10 col-xxl-10">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane show active fadeUp" id="v-pills-payed" role="tabpanel" aria-labelledby="v-pills-lan-tab" tabindex="0">
                        <?php require_once(dirname(__FILE__) . '/partials/payed-members.php'); ?>
                    </div>
                    <div class="tab-pane fadeUp" id="v-pills-pending" role="tabpanel" aria-labelledby="v-pills-online-tab" tabindex="0">
                        <?php require_once(dirname(__FILE__) . '/partials/pending-members.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once dirname(__FILE__) . "/partials/create-member-modal.php"; ?>

</div>