<?php 

?>

<table class="widefat fixed striped">
    <thead>
        <th>Navn:</th>
        <th>Gamertag:</th>
        <th>Medlemsskab</th>
    </thead>
    <tbody>
        <?php
            foreach($members as $member) {
                ?>
                    <tr>
                        <td><?php echo $member["name"]; ?></td>
                        <td><?php echo $member["gamertag"]; ?></td>
                        <td><?php echo $member["membership"]; ?></td>
                    </tr>
                <?php
            }
        ?>
    </tbody>
</table>