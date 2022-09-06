<?php
    if( $notPayedMembers )
    {
        ?>  <br>
            <h1>Afventende medlemmer <small>(<?php echo count($notPayedMembers); ?>)</small></h1><br>
            <table class="widefat fixed striped">
                <thead>
                    <th>Navn</th>
                    <th>Medlemsnummer</th>
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