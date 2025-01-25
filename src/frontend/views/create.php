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

<div class="modal fade modal-lg" id="membershipFailedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">  
                <h3>Der skete en fejl</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Der skete en fejl, vi kunne begynde din indmeldings process, prøv igen senere.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Luk</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade modal-lg" id="membershipCreatedModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Du er næsten færdig</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Du næsten færdig færdig med din indmelding i Danish Xbox League, gå videre for at færdiggøre din tilmelding</p>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Færdiggør tilmelding</button> -->
                <a href="https://danishxboxleague.unioo.info/subscriptions" class="btn btn-success">Gå videre</a>
            </div>
        </div>
    </div>
</div>