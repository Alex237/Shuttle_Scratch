<div id="content" class="animated">
    <div class="container">

        <form id="login-form" class="huge-form" method="post">
            <div class="jumbotron col-md-offset-2 col-md-8">
                <h1><i class="fa-rocket"></i>&nbsp;Shuttle</h1>
            </div>
            <div class="row">
                <div class="form-group col-md-offset-2 col-md-4">
                    <input type="email" name="email" value="<?=set_value('email');?>" class="form-control input-lg" placeholder="Email">
                    <?=form_error('email');?>
                </div>
                <div class="form-group col-md-4">
                    <input type="password" name="password" value="<?=set_value('password');?>" class="form-control input-lg" placeholder="Mot de passe">
                    <?=form_error('password');?>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-sm-offset-4 col-sm-4">
                    <button type="submit" class="btn btn-lg btn-block btn-primary">Connexion</button>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-xs-12" style="text-align: center;">
                    Vous n'avez pas de compte ? <a href="register" class="dynamic">Inscrivez-vous</a>
                </div>
            </div>
        </form>
    </div>

    <div id="home-demo">
        <div class="container">
            <div class="row">
                <div class="col-md-offset-1 col-md-5">
                    <h2>Titre truc</h2>
                    <p>Suspendisse potenti. Pellentesque mi purus, sodales vitae quam quis, auctor facilisis tortor. Aliquam iaculis at libero eu consequat. Aliquam feugiat at felis eget ultricies. Maecenas accumsan, orci quis blandit facilisis, neque est mollis quam, vel molestie ante orci et augue. Nullam lectus mauris, congue laoreet malesuada sed, placerat ut lectus. Nam lobortis est eget mauris gravida suscipit. Fusce id faucibus massa.</p>
                </div>
                <div class="col-md-5">
                    <h2>Bidule machin</h2>
                    <p>Curabitur vel pellentesque lacus. Nam dictum vel dolor mattis consectetur. In eget nibh quis libero tincidunt rutrum. In hac habitasse platea dictumst. Donec feugiat lacus sit amet ante blandit semper. Pellentesque pretium, lacus ut accumsan pulvinar, libero mi sollicitudin eros, vel adipiscing ipsum sem at justo. Nulla molestie justo sed tortor tempus vehicula. </p>
                </div>
            </div>
        </div>
    </div>
</div>