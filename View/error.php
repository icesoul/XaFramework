<html>
    <head>

        <style>
            body{background-color: #F5F5F5;margin:100px 20%;font-size: 120%}
            #errorBox{border:1px solid #915656}
            .boxtitle{border-bottom:1px solid #915656;background-color: #DBB2B2;padding:10px;font-size:20px;font-weight: bold}
            #message{padding:20px;background-color: #DBD3D3;}
            .additional{margin:20px 3%;background-color: #E3DEDE;padding:15px}
        </style>
    </head>

    <body>
        <div id="errorBox">
            <div class="boxtitle"><?= $this->title ?></div>
            <div id="message">
                <?= $this->msg ?>
                <div class="additional">
                    File: <?= $this->file ?><br/>
                    Line: <?= $this->line ?><br/>
                    Class: <?= $this->class ?><br/>
                    Function: <?= $this->function ?><br/>
                </div>
            </div>

            <div class="boxtitle">Backtrace</div>
            <div id="backtrace">

            </div>
            <div id="message">
                <? foreach ($this->backtrace as $trace): ?>
                    <div class="additional">
                        File: <?= @$trace['file'] ?> (Line: <?= @$trace['line'] ?>)<br/>
                        <? if (isset($trace['class'])): ?>
                            <?= @$trace['class'] ?>::<?= @$trace['function'] ?>(...)<br/>
                        <? else: ?>
                            <?= $trace['function'] ?><br/>
                        <? endif ?>
                    </div>
                <? endforeach ?> 


            </div>
        </div>
    </body>

</html>