<table class="widefat fixed striped">
    <thead>
        <th>Navn:</th>
        <th>Gamertag:</th>
    </thead>
    <tbody>
        <?php
        if( $members ) {
            foreach($members as $member) {
                ?>
                    <tr>
                        <td><?php echo $member->name; ?></td>
                        <td><?php echo $member->gamertag; ?></td>
                    </tr>
                <?php
            }
        } else {
            ?>
                <div class="alert alert-danger">Du har ingen afventende medlemmer</div>
            <?php
        }
        ?>
    </tbody>
</table>