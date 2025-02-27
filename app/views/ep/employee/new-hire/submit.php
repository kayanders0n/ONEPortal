<style>
    .translate-text {
        font-size:0.75em;
        font-style: italic;
    }
</style>
<div class="main">
    <?php if ($data['results']['result']): ?>
    <h2 class="page-header">Thank you! Your paperwork has been submitted!<br/><span class="translate-text">¡Gracias! ¡Su papeleo ha sido enviado!</span></h2>
    <?php else: ?>
      <h2 class="page-header">Sorry! There was a problem generating your paperwork!<br/><span class="translate-text">¡Lo siento! ¡Hubo un problema al generar su papeleo!</span></h2>
    <?php endif; ?>
    <div class="panel">
        <div class="panel-body">
            <div class="col-md-6">
                <p style="font-size: 1.75em;">
                <?php if ($data['results']['result']): ?>
                Please wait for the HR department to help you with the next steps of your paperwork.<br/>
                  <span class="translate-text">Espere a que el departamento de recursos humanos le ayude con los siguientes pasos de su papeleo.</span>
                <?php else: ?>
                <h4 class="page-header">Please let the HR department know that there was a problem generating your paperwork!</h4>
                  <span class="translate-text">¡Informe al departamento de recursos humanos que hubo un problema al generar su papeleo!</span>
                <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>



