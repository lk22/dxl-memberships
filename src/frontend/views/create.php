<div class="dxl-membership-frontend membership-assigning">
    <form action="" class="frontendCreateMemberForm">
        <div class="form-group email">
            <label for="member-email">
                E-mail:
            </label>
            <div class="input">
                <input type="email" name="member_email" id="member-email" placeholder="indtast email" required>
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