<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Install GoCart v3</title>
<link href="<?php echo base_url('../assets/css/bootstrap.min.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('../assets/css/font-awesome.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo base_url('../assets/js/jquery-2.1.3.min.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('../assets/js/bootstrap.min.js');?>"></script>

</head>

<body>



<div class="container">
    <div class="page-header">
        <h2>Thanks for Installing GoCart</h2>
    </div>


    <div class="alert alert-warning">
        <p>
            Please be aware that <strong>URL rewriting is a requirement for GoCart</strong>.<br>
            By default GoCart comes with support for Apache with MOD_REWRITE.
        </p>

        <p>
            Though it's not a requirement by default GoCart expects to be 
            installed in the base directory of your domain name.
        </p>
    </div>

    <?php if(isset($errors))
    {
        echo $errors;
    }
    ?>


    <form action="<?php echo base_url();?>" method="post" accept-charset="utf-8">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                
                <div class="form-group">
                    <label for="hostname-label">Host Name</label>
                    <?php echo form_input(['name'=>'hostname', 'class'=>'form-control', 'value'=>set_value('hostname')]);?>
                </div>
                <div class="form-group">
                    <label for="database-name-label">Database Name</label>
                    <?php echo form_input(['name'=>'database', 'class'=>'form-control', 'value'=>set_value('database')]);?>
                </div>
                <div class="form-group">
                    <label for="control-label">Username</label>
                    <?php echo form_input(['name'=>'username', 'class'=>'form-control', 'value'=>set_value('username')]);?>
                </div>
                <div class="form-group">
                    <label for="password-label">Password</label>
                    <?php echo form_input(['name'=>'password', 'class'=>'form-control', 'value'=>set_value('password')]);?>
                </div>
                <div class="form-group">
                    <label for="database-prefix-label">Database Table Prefix (ex. gc_)</label>
                    <?php echo form_input(['name'=>'prefix', 'class'=>'form-control', 'value'=>set_value('prefix')]);?>
                </div>

                <div class="alert alert-warning">
                    <p>
                        <strong>Default Login Credentials</strong><br> Username: admin<br>Password: admin
                    </p>
                </div>
                <button id="btn_step1" class="btn btn-primary" type="submit">Install</button>
            </div>
        </div>
    </form>
</div>

<footer>
    <div class="container">
        <div style="text-align:center">
            <a href="http://gocartdv.com" target="_blank">
                Driven by GoCart
            </a>
        </div>
    </div>
</footer>

</body>
</html>
