<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="<?= base_url('assets/libs/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
    <div class="container">
        <div class="row justify-content-md-center" style="margin-top: 45px;">
            <div class="col-md-4">
                <form action="<?= site_url('auth/login') ?>" method="post">
                    <?= csrf_field() ?>

                    <?php if(!empty(session()->getFlashdata('fail'))) : ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('fail') ?></div>
                    <?php endif ?>

                    <?php if(!empty(session()->getFlashdata('success'))) : ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif ?>

                    <div class="form-group mb-4">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" placeholder="Enter your email" value="<?= set_value('email') ?>" />
                        <span class="text-danger"><?= isset($validation) ? display_error($validation, 'email') : '' ?></span>
                    </div>
                    <div class="form-group mb-4">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Enter you password" />
                        <span class="text-danger"><?= isset($validation) ? display_error($validation, 'password') : '' ?></span>
                    </div>
                    <div class="form-group d-grid">
                        <button type="submit" class="btn btn-block btn-primary">Login</button>
                    </div>
                </form>
                <br>
                <a href="<?= site_url('auth/register') ?>">Have no account, new account</a>
            </div>
        </div>
    </div>
    <script src="assets/libs/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>