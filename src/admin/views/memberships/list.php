<div class="dxl-admin-members memberships-list-container">
    <div class="header">
        <h1 class="headline">Kontingenter</h1>
        <div class="actions">
            <button class="button-primary create-new-membership-btn">Opret kontingent <span class="dashicons dashicons-plus"></span></button>
        </div>
    </div>
    <div class="content">
        <?php if( $memberships ) { ?>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Kontingent</th>
                    <th>længde</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    foreach($memberships as $membership) {
                        ?>  
                            <tr>
                                <td><a href="<?php echo generate_dxl_subpage_url(['action' => 'details', 'id' => $membership->id]); ?>"><?php echo $membership->name; ?></a></td>
                                <td>
                                    <?php echo $membership->length ?> måneder
                                </td>
                                <td>
                                    <div class="actions">
                                        <button class="delete-membership-button button-primary" data-membership="<?php echo $membership->id ?>">fjern kontingent</button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
        <?php } else {
            ?>
                <div class="alert alert-danger"><span class="dashicons dashicons-warning"></span> Der er ingen registreret kontingenter</div>
            <?php
        } ?>
    </div>

    <div class="modal createMembershipsModal hidden" role="dialog">
        <div class="modal-header">
            <h1>Opret medlemsskab</h1>
        </div>
        <div class="modal-body">
            <div class="validated-message hidden"></div>
            <form action="" class="createMembershipForm">
                <div class="left-form">
                    <div class="form-control">
                        <h4>
                            <label for="membership-name">Kontingent</label>
                        </h4>
                        <input type="text" id="membership-name" placeholder="Navngiv kontingent" required>
                    </div>
    
                    <div class="form-control">
                        <h4>
                            <label for="membership-length">Kontingent varighed i antal måneder</label>
                        </h4>    
                        <input type="number" id="membership-length" pattern="[1-2]{2}" required>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="button-primary close-modal">Luk <span class="dashicons dashicons-no"></span></button>
            <button class="button-primary submit-create-membership">Opret kontingent</button>
        </div>
    </div>
    <div class="overlay hidden"></div>
</div>