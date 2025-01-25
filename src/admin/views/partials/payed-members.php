<?php if( $members ) { ?>
        <h1>Betalte medlemmer <small>(<?php echo count($members) ?>)</small></h1>
        <br>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>E-mail</th>
                    <th>Køn</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    foreach($members as $member) {
                        // $membership = $wpdb->get_row(
                        //     $wpdb->prepare(
                        //         "SELECT name from " . $wpdb->prefix . "memberships WHERE id = %d",
                        //         $member->membership
                        //     )
                        // );

                        // $is_payed = ($member->is_payed == 1) ? "Betalt kontingent" : "Mangler betaling";
                        // $profile_activated = ($member->profile_activated == 1) ? "Aktiveret" : "Ikke aktiv";

                        ?>
                            <tr data-member="<?php echo $member->id ?>">
                                <td>
                                    <a href="<?php echo generate_dxl_subpage_url(['action' => 'details', 'id' => $member->id]); ?>">
                                        <?php echo $member->email; ?>
                                    </a>
                                    <div class="actions hidden">
                                        <span class="edit">
                                            <a href='<?php echo generate_dxl_subpage_url(["action" => "details", "id" => $member->id]); ?>'>Rediger</a>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php echo $member->gender; ?>
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