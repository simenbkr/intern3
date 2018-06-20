<div class="tilbakemelding">
    <?php if (isset($_SESSION['success']) && isset($_SESSION['msg'])) { ?>

        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php echo $_SESSION['msg']; ?>
        </div>
        <p></p>
        <?php
    } elseif (isset($_SESSION['error']) && isset($_SESSION['msg'])) { ?>
        <div class="alert alert-danger fade in" id="danger" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php echo $_SESSION['msg']; ?>
        </div>
        <p></p>
        <?php
    }
    unset($_SESSION['success']);
    unset($_SESSION['error']);
    unset($_SESSION['msg']);
    ?></div>