<div class="dxl-admin-members memberships-list-container">
    <div class="header">
        <h1 class="headline">Kontingenter</h1>
        <div class="actions">
            <button class="button-primary create-new-membership-btn" data-bs-toggle="modal" data-bs-target="#createMembershipsModal">Opret kontingent <span class="dashicons dashicons-plus"></span></button>
        </div>
    </div>
    <div class="content">
        <?php if( $memberships ) { ?>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Kontingent</th>
                    <th>længde</th>
                    <th>pris</th>
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
                                    <?php echo $membership->price ?> DKK
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

    <div class="modal modal-lg fade fadeInUp" id="createMembershipsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Opret kontingent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Udfyld felterne nedenfor for at registrer nyt kontingent</p>
                <form action="#" class="create-membership-form">
                    <div class="form-group mb-4">
                        <input type="text" class="form-control" id="membership-name" placeholder="Kontingent navn" required>
                    </div>

                    <div class="form-group mb-4">
                        <input type="number" class="form-control" id="membership-length" placeholder="Varighed: antal måneder" required>
                    </div>

                    <div class="form-group">
                        <input type="number" class="form-control" id="membership-price" placeholder="Kontingent pris" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="button-primary mr-4" data-bs-dismiss="modal">Close</button>
                <button type="button" class="button-primary submit-create-membership">Opret kontingent</button>
            </div>
            </div>
        </div>
    </div>

</div>