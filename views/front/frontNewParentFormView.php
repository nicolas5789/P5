<?php ob_start(); ?>

<div class="container">
    <div class="py-5 text-center">
        <img class="d-block mx-auto mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
        <h2>Formulaire d'inscription PARENTS</h2>
        <p class="lead">Remplissez les champs ci dessous pour vous inscrire</p>
    </div>
</div>

<div id="formulaire_newParent" style="text-align: center;">
    <form action="index.php?action=addParent" method="POST">
        <label for="pseudo"> Pseudo  : <input type="text" name="pseudo" id="pseudo" required> </label> <br/> 
        <label for="nom"> Nom  : <input type="text" name="nom" id="nom" required> </label> <br/> 
        <label for="prenom"> Prenom  : <input type="text" name="prenom" id="prenom" required> </label> <br/> 
        <label for="email"> Adresse email  : <input type="email" name="email" id="email" required> </label> <br/> 
        <label for="confirm_email"> Confirmation adresse email  : <input type="email" name="confirm email" id="confirm email" required> </label> <br/> 
        <label for="password"> Mot de passe : <input type="password" name="password" id="password" required> </label>  <br/> 
        <label for="confirm_password"> Confirmation mot de passe  : <input type="password" name="confirm password" id="confirm password" required> </label> <br/> 

        <label for="departement"> Département  : 
            <select class="deptSelect" required name="departement" id="departement">
                <option value="75">Paris</option>
                <option value="78">Yvelines</option>
                <option value="91">Essonne</option>
                <option value="92">Hauts de Seine</option>
                <option value="93">Seine Saint Denis</option>
                <option value="94">Val de Marne</option>
                <option value="95">Val d'Oise</option>
                <option value="77">Seine et Marne</option>
            </select> 
        </label> <br/> 


        <label for="ville"> Ville : 
            <select class="cityContainer" type="text" name="ville" id="ville" required> 
            </select>
        </label>  <br/> 


       
     
        <input type="submit" value="Envoyer"/>      

    </form>

    <?php if(isset($_SESSION['form_message'])) {
        echo $_SESSION['form_message'];
    } ?>
</div>



<?php $content = ob_get_clean(); ?>

<?php require("views/template.php"); ?>