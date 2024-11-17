<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Certification</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/Css">
        @charset "UTF-8";

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        a {
            color: #6f6f6f;
            text-decoration: none;
        }


        address {
            font-weight: 100;
            font-size: 14pt;
            font-style: normal;
            line-height: 135%;
        }

        h2,
        h3 {
            font-weight: 300
        }

        ul {
            color: #58595B;
            text-decoration: none;
            list-style-type: none;
            font-size: 9pt;
            font-weight: 300;
            text-align: right
        }

        .cert-container {
            position: relative;
            /* padding: 45px; */
            /* border: 1px solid #6f6f6f; */
            width: 1056px;
            height: 816px;
            /* background-image: url(images/mt_icon_grayscale.jpg); */
            background-repeat: no-repeat;
            background-size: cover;
            margin: auto;
        }

        /* Certificate Outer Border (Gray) */
        .border-gray {
            padding: 5px;
            border: 3px solid #58595B;
        }

        /* Certificate Inner Border (red) */
        .border-red {
            border: 3px double #CE202F;
        }

        .content {
            padding: 20px;
            height: 750px;
            text-align: center;
        }

        /* Certificate, Host Server, and LMS IDs */
        .credentials {
            position: absolute;
            right: 100px;
            top: 120px;
        }

        .copytext-container {
            position: absolute;
            left: 190px;
            top: 275px;
            text-align: left;
            line-height: 100%;
        }

        .congrats-copytext {
            margin-bottom: 70px;
        }

        .course-copytext {
            margin-bottom: 65px;
        }

        .address-copytext {
            line-height: 150%;
        }

        #mt-logo {
            position: absolute;
            display: inline-block;
            width: 50px;
            right: 800px;
            top: 105px;
        }

        #mt-stamp {
            position: absolute;
            width: 50px;
            right: 130px;
            top: 550px;
        }

        #mt-site {
            color: #CE202F;
            font-size: 14pt;

        }

        #user-id-string {
            line-height: 7px;
        }

        #course-id-string {
            line-height: 7px;
        }

        /* @media print {
            @page {
                size: letter landscape;
                margin: 0;
                padding: 0;
            }

            .cert-container {
                border: none;
            }

            /*
  background-image {
    image-resolution: 300dpi;
    }
*/
    </style>
</head>

<body>
    <div class="cert-container">
        <div class="border-gray">
            <div class="border-red">
                <div class="content">
                    <img
                        id="mt-logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('theme/admin/assets/images/Union_Blue.png'))) }}"
                        alt="Logo Goes Here" />
                    <img id="mt-stamp" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('theme/admin/assets/images/Union_Yellow.png'))) }}"
                        alt="Certified Stamp" />

                    <ul class="credentials">
                        <li>
                            <p id="cert-id">Certificate Id: <span>{{ $certificate->code }}</span></p>
                        </li>
                        <li>
                            <p id="lms-id">Learning Management System Id: <span>{{ $course->code }}</span></p>
                        </li>
                    </ul>

                    <div class="copytext-container">
                        <div class="congrats-copytext">
                            <h1>Certificate of Completion</h1><br>
                            <h2>Congratulations <span>{{ $user->name }}</span></h2><br>
                        </div>

                        <div class="course-copytext">
                            <h1><span>{{ $course->name }}</span></h1><br>
                            <h2>Course Completed on: <span>{{ $certificate->completion_date }}</span></h2><br>
                            <h3 id="course-id-string">Course code: <span>{{ $course->code }}</span></h3>

                        </div>
                        <div class="address-copytext">
                            <address>Company Name <br> Company Street Address <br> City, State Zip</address>
                            <a href="#" id="mt-site"><em>coursea.com</em></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>