<div class="dxl dxl-admin-members members-list-container">
    <div class="header">
        <div class="logo">
            <img height="100" src="http://localhost:8888/dxl-v2/wp-content/uploads/2022/03/cropped-cropped-DXL-LOGO-Hjemmeside_192x192.png" alt="">
        </div>
        <h1 class="headline">Medlemshåndtering</h1>
        <div class="actions">
            <button class="button-primary export-members">Eksporter medlemmer</button>
            <button class="button-primary search-members-btn">Søg <span class="dashicons dashicons-filter"></span></button>
            <button class="button-primary create-new-member-btn" data-bs-toggle="modal" data-bs-target="#createMemberModal">Opret medlem <span class="dashicons dashicons-plus"></span></button>
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
                <div class="searchbar closed">
                    <h1>Søg medlemmer <span><button class="search-members-btn button-primary">Luk <span class="dashicons dashicons-no"></span></button></span></h1>
                    <hr>
                    <form action="#" class="memberListSearchForm">
                        <div class="form-group">
                            <p>Vælg felt</p>
                            <select name="search-field" id="search-field-selection">
                                <option value="0">Vælg søge kriterie</option>
                                <option value="name">Navn</option>
                                <option value="email">E-mail</option>
                                <option value="gamertag">Gamertag</option>
                                <option value="city">By</option>
                                <option value="municipality">Kommunne</option>
                            </select>
                        </div>
                        <div class="form-group search-field">
                            <input type="text" placeholder="Search member name here" name="member-name-field" id="member-name-field">
                        </div>
                        <button class="search-member-button button-primary">Søg <span class="dashicons dashicons-filter"></span></button>
                        <p>tryk på søge knap eller enter tasten</p>
                    
                    </form>
        
                    <div class="search-member-list">
                        
                    </div>
                </div>
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