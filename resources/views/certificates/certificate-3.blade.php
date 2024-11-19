<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        .serif {
            font-family: serif;
        }

        .texto {
            margin: 0;
        }

        .negrito {
            font-weight: 700
        }

        .negrito-2 {
            font-weight: 600
        }

        .sublinhar {
            text-decoration: underline;
        }

        .center {
            text-align: center
        }

        .esquerda {
            text-align: right;
        }

        .overline {
            text-decoration: overline;
        }

        .quebra_linha {
            display: block;
        }

        /*configurações de fonts*/
        .font-17 {
            font-size: 17px;
        }

        .font-24 {
            font-size: 24px;
        }

        .font-36 {
            font-size: 36px;
        }

        .font-40 {
            font-size: 40px;
        }

        /*confifurações de margin*/
        .padding_top_35 {
            padding-top: 35px;
        }

        .margin_bottom_35 {
            margin-bottom: 35px;
        }

        .altura_linhas_19 {
            line-height: 19px;
        }

        .altura_linhas_35 {
            line-height: 35px;
        }

        .altura_linhas_25 {
            line-height: 25px;
        }

        .caixa_informacoes_aluno_cea {
            font-size: 22px;
            text-align: justify;
            padding-right: 46px;
        }

        .caixa_informacoes_aluno_cea_2 {
            font-size: 22px;
            text-align: justify;
            padding-right: 46px;
            padding-left: 46px;
        }

        .caixa_informacoes_aluno_cea p {
            margin-top: 5px;
        }

        .assinatura_cea {
            line-height: 22px;
            font-size: 22px;
            padding-top: 22px;
        }

        .titulo_cea_2 {
            margin-top: 35px;
            margin-bottom: 35px
        }

        .data_entrega_cea {
            padding-top: 25px;
            padding-bottom: 43px;
        }

        /*configurações de exibição da pagina e conteudo*/
        .certificado_conteudo {
            -webkit-print-color-adjust: exact;
            background-image: url(https://image.ibb.co/eCPgQw/certificado_proesc_1.png);
            height: 755px;
            width: 1085px;
            background-repeat: no-repeat
        }

        .certificado_pagina {
            padding: 5mm;
            width: 1085px;
            margin: 30px auto;
            box-shadow: .5px .5px 7px #000;
            border-radius: 2px;
            overflow: hidden;
        }

        /*area de configuraçõa da pagina*/
        @page {
            size: 297mm 210mm;
            margin: 5mm;
            size: landscape
        }

        body {
            margin: 0;
            padding: 0px !important;
            font-family: 'Open Sans', sans-serif
        }

        p {
            margin: 0px;
        }

        /*SEMPRE DEIXAR NO FIM DO CODIGO configuração de impresão*/
        @media print {
            .certificado_pagina {
                padding: 0;
                background: transparent;
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                -webkit-box-shadow: none
            }
        }
    </style>
</head>

<body>
    <div>
        <div class="certificado_pagina">
            <div class="certificado_conteudo">
                <div class="row end-xs">
                    <div class="col-xs-10">
                        <div class="box">
                            <p class="padding_top_35 center">
                                <span class="font-40 negrito  quebra_linha serif"> CEA – COLEGIO AMAPAENSE </span>
                            </p>
                            <p class="center altura_linhas_19">
                                <span class="font-17 quebra_linha">MACAPÁ-AP – BRASIL</span>
                                <span class="font-17 quebra_linha">ENTIDADE MANTENEDORA: F.L. BITENCOURT</span>
                                <span class="font-17 quebra_linha">ATO DE RECONHECIMENTO nº 00/2000 – CXX- AP</span>
                            </p>
                        </div>
                        <div class="box">
                            <p class="padding_top_35 margin_bottom_35 center">
                                <span class="font-31 negrito-2  quebra_linha">CONCLUSÃO DO 2º segmento do ENSINO
                                    fundamental</span>
                            </p>
                            <div class="caixa_informacoes_aluno_cea serif">
                                <p class="esquerda">Certificamos que de acordo com a legislação em vigor, o (a) aluno
                                    (a)</p>
                                <p>
                                    <span class="sublinhar negrito">ROMULO MENDES SOARES JUNIOR, </span> Filho(a) de
                                    <span class="sublinhar negrito">ROMULO MENDES SOARES</span> e <span
                                        class="sublinhar negrito">WANYLZE ALBERTO BORGES</span> Natural de <span
                                        class="sublinhar negrito">MACAPÁ</span> UF <span class="sublinhar negrito">
                                        AP</span> Nascido(a) em <span class="sublinhar negrito">01/10/1993</span>
                                    concluiu no ano letivo de 2017 o 1º ANO do ENSINO MÉDIO.
                                </p>
                                <p>
                                    Assim, conferimos-lhe o presente <span class="negrito">Certificado</span>, de acordo
                                    com a Lei 9.394/96, para usufruir de seus direitos em âmbito nacional.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row end-xs">
                    <div class="col-xs-10">
                        <div class="box">
                            <div class="caixa_informacoes_aluno_cea">
                                <p class="esquerda data_entrega_cea">
                                    MACAPÁ - AP, 10 DE NOVEMBRO DE 2017
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row end-xs">
                    <div class="col-xs-10">
                        <div class="box">
                            <div class="assinatura_cea serif">
                                <p class="negrito center">____________________________</p>
                                <p class="negrito center">Concluinte</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row end-xs">
                    <div class="col-xs-5">
                        <div class="box">
                            <div class="assinatura_cea serif">
                                <p class="negrito center">____________________________</p>
                                <p class="negrito center">Diretora</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-5">
                        <div class="box">
                            <div class="assinatura_cea serif">
                                <p class="negrito center">____________________________</p>
                                <p class="negrito center">Secretária</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>
