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
