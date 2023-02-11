<div class="dxl-admin-members member-details-container">
    <div class="header">
        <h2><?php echo $member->name; ?> <span><small>(<?php echo $membership->name ?? '' ?>)</small></span></h2>
        <div class="actions">
            <a href="<?php echo generate_dxl_admin_page_url('dxl-members'); ?>" class="button-primary">Gå tilbage</a>
            <button class="button-primary search-members-btn">Søg <span class="dashicons dashicons-filter"></span></button>
            <a href="#" class="button-primary open-edit-member-modal-btn" data-bs-toggle="modal" data-bs-target="#editMemberModal">Rediger medlem <span class="dashicons dashicons-edit"></span></a>
        </div>
    </div>
    <div class="content">
        <div class="searchbar closed">
            <h1>Søg medlemmer <span><button class="search-members-btn button-primary">Luk</button></span></h1>
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
                <button class="search-member-button button-primary">Søg</button>
            </form>

            <div class="search-member-list">
                
            </div>
        </div>
        <div class="member-details details">
            <div class="left-details">
                <h1 class="fw-normal">Medlems informationer</h1>
                <?php
                    foreach([
                        "Medlemsnummer" => $member->member_number,
                        "Gamertag" => $member->gamertag,
                        "Adresse" => $member->address,
                        "Postnummer" => $member->zipcode,
                        "Bynavn" => $member->city,
                        "Kommune" => $member->municipality,
                        "Email" => $member->email,
                        "Fødselsår" => $member->birthyear
                    ] as $key => $value) {
                        ?>
                            <div class="member-<?php echo $key ?>" style="margin-bottom: 10px">
                                <h4><?php echo $key; ?>: <small class="key-<?php echo $key; ?>"><?php echo $value; ?></small></h4> 
                            </div>
                        <?php
                    }
                ?>
            </div>
            <div class="right-details">
                <div class="approved">
                    <?php
                        if( $member->approved_date != 0 ){
                            echo "<p>Betaling godkendt: " . date('d-m-Y H:i', $member->approved_date) . "</p>";
                        }
                    ?>
                </div>
                <div class="has_payed">
                    <p>Betalings status: </p>
                    <?php 
                        if( $member->is_payed ){
                            ?>
                                <div class="status-label success"><span>betalt</span></div>
                            <?php
                        } else {
                            ?>
                                <div class="status-label danger"><span>Manger betaling</span></div> 
                            <?php
                        }
                    ?>
                <?php
                    if( $member->is_payed ) {
                        ?>
                            <div class="member-actions">
                                <?php 
                                    if( isset($profile) ) {
                                        if( $profile->is_trainer ) {
                                            ?>
                                                <button class="remove-trainer-permissions-btn button-primary" data-action="remove-trainer-permissions" data-member="<?php echo $member->id ?>">Fjern træner rettigheder</button>
                                            <?php
                                        } else {
                                            ?>
                                                <button class="assign-trainer-permissions-btn button-primary" data-action="assign-trainer-permissions" data-member="<?php echo $member->id ?>">Gør til træner</button>
                                            <?php
                                        }

                                        if( $profile->is_tournament_author ) {
                                            ?>
                                                <button class="remove-tournament-permissions-btn button-primary" data-action="remove-tournament-permissions" data-member="<?php echo $member->id ?>">Gør ikke til turnerings ansvarlig</button>
                                            <?php
                                        } else {
                                            ?>
                                                <button class="assign-tournament-permissions-btn button-primary" data-action="assign-tournament-permissions" data-member="<?php echo $member->id ?>">Gør til turnerings ansvarlig</button>
                                            <?php
                                        }
                                    }
                                ?>
                                <button class="deactivate-member-button button-primary" data-bs-toggle="modal" data-bs-target="#removePayedStatusModal" data-action="deactivate-member" data-member="<?php echo $member->id; ?>">Deaktiver medlem</button>
                                <button class="reset-member-btn button-primary" data-action="reset-member-password" data-member="<?php echo $member->id; ?>">Gendan kodeord</button>
                            </div>
                        <?php
                    }

                    if( !$member->is_payed ) {
                        ?>
                            <button class="button-primary has-payed-button" data-bs-toggle="modal" data-bs-target="#hasPayedModal">Har medlemmet betalt?</button>
                        <?php
                    }
                ?>
                </div>
                <div class="membership-activity-log mt-4">
                    <h3>Kontingent aktivitet</h3>
                    <hr>
                    <?php 
                        if ( $activities ) {
                            foreach($activities as $activity) {
                                ?>
                                    <div class="activity">
                                        <div class="row mt-2">
                                            <div class="col-2">
                                                <p><?php echo date("d-m-Y H:i:s", $activity->created_at + 7200 ) ?></p>
                                            </div>
                                            <div class="col-8">
                                                <p class="mb-0 lead"><?php echo $activity->status ?></p>
                                                <p><?php echo $activity->status_message ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-lg" id="hasPayedModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Accepter betaling på følgende medlem <?php echo $member->name ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Er du sikker på medlemmet har betalt kontingent?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="button-primary me-2" data-bs-dismiss="modal">Luk</button>
        <button type="button" class="button-primary payment-accepted-button" data-member="<?php echo $member->id; ?>" >Accepter betaling</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-lg" id="removePayedStatusModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Fjern medlemsskab</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Angiv begrundelse for anullering af kontingent på <?php echo $member->name; ?></p>
        <textarea name="cancel-reason" id="cancel-reason" cols="30" rows="10"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="button-primary me-2" data-bs-dismiss="modal">Luk</button>
        <button type="button" class="button-primary payment-removed-button" data-member="<?php echo $member->id; ?>" >fjern kontingent</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-xl" id="editMemberModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Rediger medlem</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
            <div class="validated-message hidden"></div>
            <form action="" class="adminUpdateMemberForm">
                <div class="row">
                <input type="hidden" name="member-id" id="member-id" value="<?php echo $member->id; ?>">
                <div class="left-form col-12 col-md-6">
                    <div class="form-group name">
                        <label for="member-name">Navn:</label>
                        <div class="input">
                            <input type="text" id="member-name" class="form-control" name="member_name" placeholder="indtast medlemsnavn" value="<?php echo $member->name; ?>" required>
                        </div>
                    </div>

                    <div class="form-group gamertag">
                        <label for="member-gamertag">Gamertag:</label>
                        <div class="input">
                            <input type="text" id="member-gamertag" class="form-control" name="member_gamertag" placeholder="indtast gamertag" value="<?php echo $member->gamertag; ?>" required>
                        </div>
                    </div>

                    <div class="form-group email">
                        <label for="member-email">
                            E-mail:
                        </label>
                        <div class="input">
                            <input type="email" name="member_email" class="form-control" id="member-email" placeholder="indtast email" value="<?php echo $member->email; ?>" required>
                        </div>
                    </div>

                    <div class="form-group phonenumber">
                        <label for="member_phone">Telefonnr:</label>
                        <div class="input">
                            <input type="tel", name="member_phone" class="form-control" id="member-phone" value="<?php echo $member->phone; ?>" pattern="[0-9]{8}" required>
                        </div>
                    </div>

                    <div class="form-group birth">
                        <label for="member-birthdate">
                            Fødselsdato
                        </label>
                        <div class="input">
                            <?php
                                $datetime = new DateTime($member->birthyear);
                            ?>
                            <input type="date" name="member_birthdate" class="form-control" id="member-birthdate" value="<?php echo date("Y-m-d", strtotime($member->birthyear)); ?>" required>
                        </div>
                    </div>

                    <div class="form-group member-gender">
                        <label for="member-gender">Vælg køn</label>
                        <div class="input">
                            <select name="member_gender" class="form-control" id="member-gender">
                                <option value="<?php echo $member->gender ?>"><?php echo $member->gender; ?></option>
                                <?php 
                                    foreach(["mand", "kvinde", "andet"] as $gender) {
                                        if( $gender == $member->gender ){
                                            continue;
                                        }
                                        echo "<option value='" . $gender . "'>" . $gender . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group member-number">
                        <label for="member-number">Medlemsnummer</label>
                        <div class="input">
                            <input type="number" class="form-control" name="member_number" id="member-number" value="<?php echo $member->member_number; ?>" required>
                        </div>
                    </div>

                </div>
                <div class="right-form col-12 col-md-6">
                    <h3>Adresse informationer</h3>
                    <div class="form-group address">
                        <label for="member-adress">Adresse</label>
                        <div class="input">
                            <input type="text" class="form-control" name="member_adress" id="member-address" value="<?php echo $member->address; ?>" required>
                        </div>
                    </div>

                    <div class="form-group zipcode">
                        <label for="member-zipcode">Postnummer</label>
                        <div class="input">
                            <input type="text" class="form-control" name="member_zipcode" id="member-zipcode" value="<?php echo $member->zipcode; ?>" required>
                        </div>
                    </div>

                    <div class="form-group town">
                        <label for="member-town">Bynavn:</label>
                        <div class="input">
                            <input type="text" class="form-control" name="member_town" id="member-town" value="<?php echo $member->city; ?>" required>
                        </div>
                    </div>

                    <div class="form-group municipality">
                        <label for="member-municipality">Komunne:</label>
                        <div class="input">
                            <input type="text" class="form-control" name="member_municipality" id="member-municipality" value="<?php echo $member->municipality ?>" required>
                        </div>
                    </div><br>

                    <h3>Vælg medlemsskab</h3>
                    <div class="form-group member-membership">
                        <div class="input">
                            <select name="member_membership" class="form-control" id="member-membership">
                                <option value="<?php echo $member->membership ?>"><?php echo $membership->name ?></option>
                                <?php 
                                    foreach($memberships as $membership) 
                                    {
                                        if( $membership->id !== $member->membership) {
                                            ?> <option value="<?php echo $membership->id ?>"><?php echo $membership->name; ?></option> <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <p class="lead mb-1 mt-1">Ønsker autofornyelse?</p>
                    <div class="form-group member-auto-renew">
                        <div class="input">
                            <label for="member-renew">Ja</label>
                            <input type="radio" name="member_autorenew" id="member-renew" value="1" <?php if($member->auto_renew == 1) {echo "checked='checked'";} else { echo '';} ?>>
                            <label for="member-renew">Nej</label>
                            <input type="radio" name="member_autorenew" id="member-renew" value="0" <?php if($member->auto_renew == 0) {echo "checked='checked'";} else { echo '';}?>>
                        </div>
                    </div>
                </div>
                </div>
            </form>
            
      </div>
      <div class="modal-footer">
        <button type="button" class="button-primary me-2" data-bs-dismiss="modal">Luk</button>
        <button class="button-primary submit-update-member">Opdater Medlem<span class="dashicons dashicons-edit"></span></button>
      </div>
    </div>
  </div>
</div>
