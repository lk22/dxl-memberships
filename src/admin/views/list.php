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
    
    <div class="content">
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
        <?php if( $members ) { ?>
        <h1>Betalte medlemmer <small>(<?php echo count($members) ?>)</small></h1>
        <br>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Navn</th>
                    <th>Medlemsnummer</th>
                    <th>E-mail</th>
                    <th>Medlemsskab</th>
                    <th>Betalings status</th>
                    <th>Profil status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    foreach($members as $member) {
                        $membership = $wpdb->get_row(
                            $wpdb->prepare(
                                "SELECT name from " . $wpdb->prefix . "memberships WHERE id = %d",
                                $member->membership
                            )
                        );

                        $is_payed = ($member->is_payed == 1) ? "Betalt kontingent" : "Mangler betaling";
                        $profile_activated = ($member->profile_activated == 1) ? "Aktiveret" : "Ikke aktiv";

                        ?>
                            <tr data-member="<?php echo $member->id ?>">
                                <td>
                                    <a href="<?php echo generate_dxl_subpage_url(['action' => 'details', 'id' => $member->id]); ?>">
                                        <?php echo $member->name; ?>
                                    </a>
                                    <div class="actions hidden">
                                        <span class="edit">
                                            <a href='<?php echo generate_dxl_subpage_url(["action" => "details", "id" => $member->id]); ?>'>Rediger</a>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php echo $member->member_number; ?>
                                </td>
                                <td>
                                    <?php echo $member->email; ?>
                                </td>
                                
                                <td>
                                    <?php echo ($membership) ? $membership->name : ""; ?>
                                </td>

                                <td>
                                    <?php echo $is_payed; ?>
                                </td>

                                <td>
                                    <?php echo $profile_activated; ?>
                                </td>
                                
                            </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
        <?php } else {
            ?>
                <div class="alert alert-danger"><span class="dashicons dashicons-warning"></span> Der er ingen registreret medlemmer i systemet</div>
            <?php
        } ?>

        <?php
            if( $notPayedMembers )
            {
                ?>  <br>
                    <h1>Ikke betalte medlemmer <small>(<?php echo count($notPayedMembers); ?>)</small></h1><br>
                    <table class="widefat fixed striped">
                        <thead>
                            <th>Navn</th>
                            <th>Email</th>
                            <th>Afventer godkendelse</th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                            <?php 
                                foreach($notPayedMembers as $member) {
                                    $awaiting = ($member->is_pending == 1) ? "Afventer" : "Afventer ikke";
                                    ?>
                                        <tr data-member="<?php echo $member->id ?>">
                                            <td>
                                                <a href="<?php echo generate_dxl_subpage_url(['action' => 'details', 'id' => $member->id]); ?>">
                                                    <?php echo $member->name; ?>
                                                </a>
                                                <div class="actions hidden">
                                                    <span class="edit">
                                                        <a href='<?php echo generate_dxl_subpage_url(["action" => "details", "id" => $member->id]); ?>'>Rediger</a>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo $member->member_number; ?>
                                            </td>
                                            <td>
                                                <?php echo $member->email; ?>
                                            </td>
                                            <td>
                                                <?php echo $awaiting; ?>
                                            </td>
                                            <td>
                                                <button class="button-primary delete-member-button" data-name="<?php $member->name; ?>" data-member="<?php echo $member->id ?>">Fjern medlem</button>
                                            </td>
                                        </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                <?php
            }
        ?>
    </div>

    <div class="modal fade modal-xl" id="createMemberModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Opret medlem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Molestiae quaerat, atque optio non, et rerum aspernatur sint necessitatibus iusto voluptatibus, ullam sequi explicabo a accusantium in dignissimos quasi eum dolor!</p>
            <div class="validated-message hidden"></div>
            <form action="" class="adminCreateMemberForm">
                <div class="left-form">
                    <div class="form-group name">
                        <label for="member-name">Navn:</label>
                        <div class="input">
                            <input type="text" class="form-control" id="member-name" name="member_name" placeholder="indtast medlemsnavn" required>
                        </div>
                    </div>

                    <div class="form-group gamertag">
                        <label for="member-gamertag">Gamertag:</label>
                        <div class="input">
                            <input type="text" class="form-control" id="member-gamertag" name="member_gamertag" placeholder="indtast gamertag" required>
                        </div>
                    </div>

                    <div class="form-group email">
                        <label for="member-email">
                            E-mail:
                        </label>
                        <div class="input">
                            <input type="email" class="form-control" name="member_email" id="member-email" placeholder="indtast email" required>
                        </div>
                    </div>

                    <div class="form-group phonenumber">
                        <label for="member_phone">Telefonnr:</label>
                        <div class="input">
                            <input type="tel", class="form-control" name="member_phone", id="member-phone" value="12345678" pattern="[0-9]{8}" required>
                        </div>
                    </div>

                    <div class="form-group birth">
                        <label for="member-birthdate">
                            Fødselsår
                        </label>
                        <div class="input">
                            <input type="date" class="form-control" name="member_birthdate" id="member-birthdate" required>
                        </div>
                    </div>

                    <div class="form-group member-gender">
                        <label for="member-gender">Vælg køn</label>
                        <div class="input">
                            <select name="member_gender" class="form-control" id="member-gender">
                                <option value="">Vælg køn</option>
                                <option value="mand">Mand</option>
                                <option value="kvinde">Kvinde</option>
                                <option value="andet">Andet</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group member-number">
                        <label for="member-number">Medlemsnummer</label>
                        <div class="input">
                            <input type="number" class="form-control" name="member_number" id="member-number" required>
                        </div>
                    </div>
                </div>
                <div class="right-form">
                    <h3>Adresse informationer</h3>
                    <div class="form-group address">
                        <label for="member-adress">Adresse</label>
                        <div class="input">
                            <input type="text" class="form-control" name="member_adress" id="member-address" required>
                        </div>
                    </div>

                    <div class="form-group zipcode">
                        <label for="member-zipcode">Postnummer</label>
                        <div class="input">
                            <input type="text" class="form-control" name="member_zipcode" id="member-zipcode" required>
                        </div>
                    </div>

                    <div class="form-group town">
                        <label for="member-town">Bynavn:</label>
                        <div class="input">
                            <input type="text" class="form-control" name="member_town" id="member-town" required>
                        </div>
                    </div>

                    <div class="form-group municipality">
                        <label for="member-municipality">Komunne:</label>
                        <div class="input">
                            <input type="text" class="form-control" name="member_municipality" id="member-municipality" required>
                        </div>
                    </div><br>

                    <h3>Vælg medlemsskab</h3>
                    <div class="form-group member-membership">
                        <div class="input">
                            <select name="member_membership" class="form-control" id="member-membership">
                                <?php 
                                    foreach($memberships as $membership) 
                                    {
                                        ?> <option value="<?php echo $membership->id ?>"><?php echo $membership->name; ?></option> <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group member-auto-renew">
                        <div class="input">
                            <label for="member-renew">Ja</label>
                            <input type="radio" name="member_autorenew" id="member-renew" value="1">
                            <label for="member-renew">Nej</label>
                            <input type="radio" name="member_autorenew" id="member-renew" value="0">
                        </div>
                    </div>
                </div>
            </form>
            
            </div>
            <div class="modal-footer">
            <button class="button-primary close-modal" data-bs-dismiss="modal">Luk <span class="dashicons dashicons-no"></span></button>
            <button class="button-primary submit-create-member">Opret Medlem</button>
            </div>
            </div>
        </div>
    </div>


</div>