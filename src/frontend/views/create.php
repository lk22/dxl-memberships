<div class="dxl-membership-frontend membership-assigning">
    <form action="" class="frontendCreateMemberForm">
        <div class="left-form">
            <div class="form-group name">
                <label for="member-name">Navn:</label>
                <div class="input">
                    <input type="text" id="member-name" name="member_name" placeholder="indtast medlemsnavn" required>
                </div>
            </div>

            <div class="form-group gamertag">
                <label for="member-gamertag">Gamertag:</label>
                <div class="input">
                    <input type="text" id="member-gamertag" name="member_gamertag" placeholder="indtast gamertag" required>
                </div>
            </div>

            <div class="form-group email">
                <label for="member-email">
                    E-mail:
                </label>
                <div class="input">
                    <input type="email" name="member_email" id="member-email" placeholder="indtast email" required>
                </div>
            </div>

            <div class="form-group phonenumber">
                <label for="member_phone">Telefonnr:</label>
                <div class="input">
                    <input type="tel", name="member_phone", id="member-phone" value="12345678" pattern="[0-9]{8}" required>
                </div>
            </div>

            <div class="form-group birth">
                <label for="member-birthdate">
                    Fødselsdato
                </label>
                <div class="input">
                    <div class="row">
                        <div class="col-4">
                            <label for="member-birthdate-day">
                                Dag
                            </label>
                            <!-- date field that validates year month and days -->
                            <input type="number" name="member_birthdate_day" id="member-birthdate-day" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label for="member-birthdate-month">
                                Måned
                            </label>
                            <!-- date field that validates year month and days -->
                            <input type="number" name="member_birthdate_month" id="member-birthdate-month" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label for="member-birthdate-year">
                                Årstal
                            </label>
                            <!-- date field that validates year month and days -->
                            <input type="number" name="member_birthdate_year" id="member-birthdate-year" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group member-gender">
                <label for="member-gender">Vælg køn</label>
                <div class="input">
                    <select name="member_gender" id="member-gender">
                        <option value="">Vælg køn</option>
                        <option value="mand">Mand</option>
                        <option value="kvinde">Kvinde</option>
                        <option value="andet">Andet</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="right-form mt-3">
            <h3>Adresse informationer</h3>
            <div class="form-group address">
                <label for="member-adress">Adresse</label>
                <div class="input">
                    <input type="text" name="member_adress" id="member-address" required>
                </div>
            </div>

            <div class="form-group zipcode">
                <label for="member-zipcode">Postnummer</label>
                <div class="input">
                    <input type="text" name="member_zipcode" id="member-zipcode" required>
                </div>
            </div>

            <div class="form-group town">
                <label for="member-town">Bynavn:</label>
                <div class="input">
                    <input type="text" name="member_town" id="member-town" required>
                </div>
            </div>

            <div class="form-group municipality">
                <label for="member-municipality">Komunne:</label>
                <div class="input">
                    <input type="text" name="member_municipality" id="member-municipality" required>
                </div>
            </div><br>

            <h3>Vælg medlemsskab</h3>
            <div class="form-group member-membership">
                <div class="input">
                    <select name="member_membership" id="member-membership">
                        <?php 
                            foreach($memberships as $membership) 
                            {
                                if( strtotime('today') > strtotime('last day of june this year') && $membership->length == 12) {
                                            
                                } else {
                                    echo "<option value=" . $membership->id .">" . $membership->name . " (" . $membership->price . " DKK)</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <!-- auto renewal field for signing up for membership -->
                <h3>Ønsker du autofornyelse?</h3>
                <div class="form-group member-auto-renewal">
                    <div class="input">
                        <select name="member-auto-renewal" id="member-auto-renewal">
                            <option value="">Vælg mulighed</option>
                            <option value="yes">Ja</option>
                            <option value="no">Nej</option>
                        </select>
                    </div>
                </div>
            </div>

            <!--
                create checkbox accept terms and conditions
            -->
            <div class="form-group member-terms mt-2">
                <div class="input">
                    <label for="member-terms">
                        <input type="checkbox" name="member_terms" id="member-terms" required>
                        Jeg accepterer <a href="/privatlivspolitik">medlems betingelser</a>
                    </label>
                </div>
            </div>

            <div class="form-group member-auto-renew mt-3">
                <div class="input">
                    <input type="submit" value="Ansøg">
                </div>
            </div>
            <div class="form-group error-message"></div>
        </div>
    </form>
    <div class="success-container hidden" style="display:none">
        <div class="headline"></div>
        <div class="message"></div>
        <a href="/">Gå tilbage</a>
    </div>
</div>