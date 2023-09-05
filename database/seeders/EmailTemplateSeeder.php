<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $this->defaultEmail();
  }

  // For Email template Module
  protected function defaultEmail()
  {
    // Email Template
    $emailTemplate = [
      'new_user' => 'New User',
      'contract_expiry' => 'Contract Expiry',
      'contract_task_reminder' => 'Contract Task Reminder',
      'reset_password' => 'Reset Password',
      'verify_email' => 'Verify Email',
      'failed_login' => 'Failed Login',
      'new_device_login' => 'New Device Login',
      'new_location_login' => 'New Location Login',
      'lost_recover_code' => 'Lost Recovery Code',
      'two_factor_code' => 'Two Factor Code',
    ];
    foreach ($emailTemplate as $slug => $eTemp) {
      $emailTemp = EmailTemplate::where('name', $eTemp)->count();
      if ($emailTemp == 0) {
        EmailTemplate::create(
          [
            'name' => $eTemp,
            'from' => env('APP_NAME'),
            'slug' => $slug,
            'created_by' => 1,
          ]
        );
      }
    }

    $defaultTemplate = [
      'new_user' => [
        'subject' => 'New User',
        'lang' => [
          'en' => '<table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
                <tbody>
                  <tr>
                    <td align="center" valign="top">
                      <!-- Table Card Open // -->
                      <table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">
                        <tbody>
                          <tr>
                            <!-- Header Top Border // -->
                            <td height="3" style="background-color:#cd545b;font-size:1px;line-height:3px;" class="topBorder">&nbsp;</td>
                          </tr>
                          <tr>
                            <td align="center" valign="top" style="padding-bottom: 20px;" class="imgHero">
                              <!-- Hero Image // -->
                              <a href="#" target="_blank" style="text-decoration:none;">
                                <img src="https://pms.afifosh.com/assets/img/mail/user-welcome.png" width="600" alt="" border="0" style="width:100%; max-width:600px; height:auto; display:block;">
                              </a>
                            </td>
                          </tr>
                          <tr>
                            <td align="center" valign="top" style="padding-bottom: 5px; padding-left: 20px; padding-right: 20px;" class="mainTitle">
                              <!-- Main Title Text // -->
                              <h2 class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">
                                Welcome to Riyadh Art!
                              </h2>
                            </td>
                          </tr>
                          <tr>
                            <td align="center" valign="top" style="padding-bottom: 30px; padding-left: 20px; padding-right: 20px;" class="subTitle">
                              <!-- Sub Title Text // -->
                              <h4 class="text" style="color:#999999; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">
                                Getting Started With Riyadh Art</h4>
                            </td>
                          </tr>
                          <tr>
                            <td align="center" valign="top" style="padding-left:20px;padding-right:20px;" class="containtTable ui-sortable">
                              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tablCatagoryLinks" style="">
                                <tbody><tr>
                                  <td align="center" valign="top" style="padding-bottom:20px;" class="catagoryLinks">
                                    <a href="#" target="_blank" style="display:inline-block;">
                                      <img src="https://pms.afifosh.com/assets/img/mail/catagory-1.png" alt="" width="60" border="0" style="height:auto; width:100%; max-width:60px; margin-left:2px; margin-right:2px">
                                    </a>
                                    <a href="#" target="_blank" style="display:inline-block;">
                                      <img src="https://pms.afifosh.com/assets/img/mail/catagory-2.png" alt="" width="60" border="0" style="height:auto; width:100%; max-width:60px; margin-left:2px; margin-right:2px">
                                    </a>
                                    <a href="#" target="_blank" style="display:inline-block;">
                                      <img src="https://pms.afifosh.com/assets/img/mail/catagory-3.png" alt="" width="60" border="0" style="height:auto; width:100%; max-width:60px; margin-left:2px; margin-right:2px">
                                    </a>
                                    <a href="#" target="_blank" style="display:inline-block;">
                                      <img src="https://pms.afifosh.com/assets/img/mail/catagory-4.png" alt="" width="60" border="0" style="height:auto; width:100%; max-width:60px; margin-left:2px; margin-right:2px">
                                    </a>
                                  </td>
                                </tr>
                              </tbody></table>
                              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableTitleDescription" style="">
                                <tbody><tr>
                                  <td align="center" valign="top" style="padding-bottom:10px;" class="mediumTitle">
                                    <!-- Medium Title Text // -->
                                    <p class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:18px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:26px; text-transform:none; text-align:center; padding:0; margin:0">
                                      Explore our trending Category
                                    </p>
                                  </td>
                                </tr>
                                <tr>
                                  <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                                    <!-- Description Text// -->
                                    <p class="text" style="color: rgb(102, 102, 102); font-family: " open="" sans",="" helvetica,="" arial,="" sans-serif;="" font-size:="" 14px;="" font-style:="" normal;="" letter-spacing:="" line-height:="" 22px;="" text-transform:="" none;="" text-align:="" center;="" padding:="" 0px;="" margin:="" 0px;"=""><b>Your Credentials are</b><br><b>Email:</b> {email}<br><b>Password:</b><span style="font-weight: 400;"> {password}<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Thank you for joining with Notify, We have more than a 6 million Readers Each Month, keeping you up to date with what’s going on in the world.
                                    </span></p>
                                  </td>
                                </tr>
                              </tbody></table>
                              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableButton" style="">
                                <tbody><tr>
                                  <td align="center" valign="top" style="padding-top:20px;padding-bottom:20px;">
                                    <!-- Button Table // -->
                                    <table align="center" border="0" cellpadding="0" cellspacing="0">
                                      <tbody><tr>
                                        <td align="center" class="ctaButton" style="background-color:#cd545b;padding-top:12px;padding-bottom:12px;padding-left:35px;padding-right:35px;border-radius:50px">
                                          <!-- Button Link // -->
                                          <a class="text" href="{app_url}" style="color:#FFFFFF; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; font-style:normal;letter-spacing:1px; line-height:20px; text-transform:uppercase; text-decoration:none; display:block">
                                            Explore Now
                                          </a>
                                        </td>
                                      </tr>
                                    </tbody></table>
                                  </td>
                                </tr>
                              </tbody></table>
                            </td>
                          </tr>
                          <tr>
                            <td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
                          </tr>
                          <tr>
                            <td align="center" valign="middle" style="padding-bottom: 40px;" class="emailRegards">
                                      <!-- Image and Link // -->
                                      <a href="#" target="_blank" style="text-decoration:none;">
                                          <img mc:edit="signature" src="https://pms.afifosh.com/assets/img/mail/signature.png" alt="" width="150" border="0" style="width:100%;
                                                max-width:150px; height:auto; display:block;">
                                      </a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                      <!-- Table Card Close// -->
                      <!-- Space -->
                      <table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
                        <tbody>
                          <tr>
                            <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                </tr>
              </tbody>
            </table>',
        ],
      ],
      'contract_expiry' => [
        'subject' => 'Contract Expired',
        'lang' => [
          'en' => '<table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
          <tbody><tr>
            <td align="center" valign="top">

              <!-- Table Card Open // -->
              <table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

                <tbody><tr>
                  <!-- Header Top Border // -->
                  <td height="3" style="background-color:#cd545b;font-size:1px;line-height:3px;" class="topBorder">&nbsp;</td>
                </tr>


                <tr>
                  <td align="center" valign="top" style="padding-bottom: 20px;" class="imgHero">
                    <!-- Hero Image // -->
                    <a href="#" target="_blank" style="text-decoration:none;">
                      <img src="https://pms.afifosh.com/assets/img/mail/notification-reminder.png" width="600" alt="" border="0" style="width:100%; max-width:600px; height:auto; display:block;">
                    </a>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom: 5px; padding-left: 20px; padding-right: 20px;" class="mainTitle">
                    <!-- Main Title Text // -->
                    <h2 class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">
                      Reminder
                    </h2>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom: 30px; padding-left: 20px; padding-right: 20px;" class="subTitle">
                    <!-- Sub Title Text // -->
                    <h4 class="text" style="color:#999999; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">
                      Your contract: {contract_subject} is expired.</h4>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-left:20px;padding-right:20px;" class="containtTable ui-sortable">

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDivider" style="">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:40px;">
                          <!-- Divider // -->
                          <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody><tr>
                              <td height="1" style="background-color:#E5E5E5;font-size:1px;line-height:1px;" class="divider">&nbsp;</td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableSmllTitle">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom:10px;" class="smllTitle">
                          <!-- Small Title Text // -->
                          <p class="text" style="color:#777777; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">Expiry Date</p>
                        </td>
                      </tr>

                      <tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="smllSubTitle">
                          <!-- Info Title Text // -->
                          <p class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:18px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:26px; text-transform:none; text-align:center; padding:0; margin:0">
                            {contract_end_date}
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableSmllTitle" style="">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom: 10px;" class="smllTitle">
                          <!-- Small Title Text // -->
                          <p class="text" style="color:#777777; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            Plan
                          </p>
                        </td>
                      </tr>

                      <tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="smllSubTitle">
                          <!-- Info Title Text // -->
                          <p class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:18px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:26px; text-transform:none; text-align:center; padding:0; margin:0">
                            PRO Platinum
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableSmllTitle" style="">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom:10px;" class="smllTitle">
                          <!-- Small Title Text // -->
                          <p class="text" style="color:#777777; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            Plan Price
                          </p>
                        </td>
                      </tr>

                      <tr>
                        <td align="center" valign="top" style="padding-bottom: 20px;" class="smllSubTitle">
                          <!-- Info Title Text // -->
                          <p class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:18px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:26px; text-transform:none; text-align:center; padding:0; margin:0">
                            $19/month
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDivider">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:40px;">
                          <!-- Divider // -->
                          <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody><tr>
                              <td height="1" style="background-color:#E5E5E5;font-size:1px;line-height:1px;" class="divider">&nbsp;</td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDescription" style="">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                          <!-- Description Text// -->
                          <p class="text" style="color:#666666; font-family:\'Open Sans\', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            The services you are used due for renewal within next 7 Days 11/06/2020. Kindly login to your account at Notify and renew them to avoid suspension.
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableButtonDate">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:5px;">
                          <!-- Button Table // -->
                          <table align="center" border="0" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                              <td align="center" class="ctaButton" style="background-color:#cd545b;padding-top:12px;padding-bottom:12px;padding-left:35px;padding-right:35px;border-radius:50px">
                                <!-- Button Link // -->
                                <a class="text" href="{contract_view_url}" target="_blank" style="color:#FFFFFF; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; font-style:normal;letter-spacing:1px; line-height:20px; text-transform:uppercase; text-decoration:none; display:block">
                                  View Contract
                                </a>
                              </td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>

                      <tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="infoDate">
                          <!-- Info Date // -->
                          <p class="text" style="color:#000000; font-family:\'Open Sans\', Helvetica, Arial, sans-serif; font-size:11px; font-weight:700; font-style:normal; letter-spacing:normal; line-height:20px; text-transform:none; text-align:center; padding:0; margin:0">
                            Expired at:&nbsp;{contract_end_date}</p>
                        </td>
                      </tr>
                    </tbody></table>

                  </td>
                </tr>

                <tr>
                  <td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>

                <tr><td align="center" valign="middle" style="padding-bottom:40px" class="emailRegards">
                            <!-- Image and Link // -->
                            <a href="#" target="_blank" style="text-decoration:none;">
                                <img mc:edit="signature" src="https://pms.afifosh.com/assets/img/mail/signature.png" alt="" width="150" border="0" style="width:100%;
    max-width:150px; height:auto; display:block;">
                            </a>
                        </td>
    </tr>
              </tbody></table>
              <!-- Table Card Close// -->

              <!-- Space -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
                <tbody><tr>
                  <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>
              </tbody></table>

            </td>
          </tr>
        </tbody></table>'
        ]
      ],
      'contract_task_reminder' => [
        'subject' => 'Contract Task Reminder',
        'lang' => [
          'en' => '<table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
          <tbody><tr>
            <td align="center" valign="top">

              <!-- Table Card Open // -->
              <table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

                <tbody><tr>
                  <!-- Header Top Border // -->
                  <td height="3" style="background-color:#cd545b;font-size:1px;line-height:3px;" class="topBorder">&nbsp;</td>
                </tr>


                <tr>
                  <td align="center" valign="top" style="padding-bottom: 20px;" class="imgHero">
                    <!-- Hero Image // -->
                    <a href="#" target="_blank" style="text-decoration:none;">
                      <img src="https://pms.afifosh.com/assets/img/mail/notification-reminder.png" width="600" alt="" border="0" style="width:100%; max-width:600px; height:auto; display:block;">
                    </a>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px;" class="mainTitle">
                    <!-- Main Title Text // -->
                    <h2 class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">
                      Reminder
                    </h2>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom:30px;padding-left:20px;padding-right:20px;" class="subTitle">
                    <!-- Sub Title Text // -->
                    <h4 class="text" style="color:#999999; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">You have a reminder about the task: {task_subject}</h4>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-left:20px;padding-right:20px;" class="containtTable ui-sortable">

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDivider">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:40px;">
                          <!-- Divider // -->
                          <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody><tr>
                              <td height="1" style="background-color:#E5E5E5;font-size:1px;line-height:1px;" class="divider">&nbsp;</td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableSmllTitle">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom:10px;" class="smllTitle">
                          <!-- Small Title Text // -->
                          <p class="text" style="color:#777777; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            Reminder Was Set By</p>
                        </td>
                      </tr>

                      <tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="smllSubTitle">
                          <!-- Info Title Text // -->
                          <p class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:18px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:26px; text-transform:none; text-align:center; padding:0; margin:0">
                            {reminder_set_by_name}
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableSmllTitle">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom:10px;" class="smllTitle">
                          <!-- Small Title Text // -->
                          <p class="text" style="color:#777777; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            Plan</p></td></tr></tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDivider">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:40px;">
                          <!-- Divider // -->
                          <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tbody><tr>
                              <td height="1" style="background-color:#E5E5E5;font-size:1px;line-height:1px;" class="divider">&nbsp;</td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDescription">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="description">
                          <!-- Description Text// -->
                          <p class="text" style="color:#666666; font-family:\'Open Sans\', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            {reminder_description}
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableButtonDate">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:5px;">
                          <!-- Button Table // -->
                          <table align="center" border="0" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                              <td align="center" class="ctaButton" style="background-color:#cd545b;padding-top:12px;padding-bottom:12px;padding-left:35px;padding-right:35px;border-radius:50px">
                                <!-- Button Link // -->
                                <a class="text" href="#" target="_blank" style="color:#FFFFFF; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; font-style:normal;letter-spacing:1px; line-height:20px; text-transform:uppercase; text-decoration:none; display:block">
                                  Go to My Renewals
                                </a>
                              </td>
                            </tr>
                          </tbody></table>
                        </td>
                      </tr>

                      <tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="infoDate">
                          <!-- Info Date // -->
                          <p class="text" style="color:#000000; font-family:\'Open Sans\', Helvetica, Arial, sans-serif; font-size:11px; font-weight:700; font-style:normal; letter-spacing:normal; line-height:20px; text-transform:none; text-align:center; padding:0; margin:0"><br></p>
                        </td>
                      </tr>
                    </tbody></table>

                  </td>
                </tr>

                <tr>
                  <td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>

                <tr><td align="center" valign="middle" style="padding-bottom:40px" class="emailRegards">
                            <!-- Image and Link // -->
                            <a href="#" target="_blank" style="text-decoration:none;">
                                <img mc:edit="signature" src="https://pms.afifosh.com/assets/img/mail/signature.png" alt="" width="150" border="0" style="width:100%;
    max-width:150px; height:auto; display:block;">
                            </a>
                        </td>
    </tr>
              </tbody></table>
              <!-- Table Card Close// -->

              <!-- Space -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
                <tbody><tr>
                  <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>
              </tbody></table>

            </td>
          </tr>
        </tbody></table>'
        ]
      ],
      'reset_password' => [
        'subject' => 'Reset Password',
        'lang' => [
          'en' => '<table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
          <tbody><tr>
            <td align="center" valign="top">

              <!-- Table Card Open // -->
              <table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

                <tbody><tr>
                  <!-- Header Top Border // -->
                  <td height="3" style="background-color:#cd545b;font-size:1px;line-height:3px;" class="topBorder">&nbsp;</td>
                </tr>


                <tr>
                  <td align="center" valign="top" style="padding-bottom: 20px;" class="imgHero">
                    <!-- Hero Image // -->
                    <a href="#" target="_blank" style="text-decoration:none;">
                      <img src="https://pms.afifosh.com/assets/img/mail/user-reset-password.png" width="600" alt="" border="0" style="width:100%; max-width:600px; height:auto; display:block;">
                    </a>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px;" class="mainTitle">
                    <!-- Main Title Text // -->
                    <h2 class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">
                      Reset Password
                    </h2>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom:30px;padding-left:20px;padding-right:20px;" class="subTitle">
                    <!-- Sub Title Text // -->
                    <h4 class="text" style="color:#999999; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">
                      We\'ve received your request to<br>change your password.
                    </h4>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-left:20px;padding-right:20px;" class="containtTable ui-sortable">

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDescription">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="description">
                          <!-- Description Text// -->
                          <p class="text" style="line-height: 22px; text-align: center; padding: 0px; margin: 0px;"><font color="#666666" face="Open Sans, Helvetica, Arial, sans-serif"><span style="font-size: 14px;">
                            Click on the button below to reset your password, you have&nbsp;</span></font><span style="text-align: var(--bs-body-text-align); font-size: 14px;"><font color="#666666" face="Open Sans, Helvetica, Arial, sans-serif">{link_expiry_time}</font></span></p><p class="text" style="color: rgb(102, 102, 102); font-family: &quot;Open Sans&quot;, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 22px; padding: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px;">&nbsp;hours to pick your password. After that, you\'ll have to ask for a new one.<br></p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDescription">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="description">
                          <!-- Description Text// -->
                          <p class="text" style="color:#666666; font-family:\'Open Sans\', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            Or using this Link: <a href="{password_reset_link}" target="_blank">&nbsp;{password_reset_link}</a>
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableButton">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:20px;">

                          <!-- Button Table // -->
                          <table align="center" border="0" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                              <td align="center" class="ctaButton" style="background-color:#cd545b;padding-top:12px;padding-bottom:12px;padding-left:35px;padding-right:35px;border-radius:50px">
                                <!-- Button Link // -->
                                <a class="text" href="{password_reset_link}" target="_blank" style="color:#FFFFFF; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; font-style:normal;letter-spacing:1px; line-height:20px; text-transform:uppercase; text-decoration:none; display:block">
                                  Reset Password
                                </a>
                              </td>
                            </tr>
                          </tbody></table>

                        </td>
                      </tr>
                    </tbody></table>

                  </td>
                </tr>

                <tr>
                  <td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>

                <tr><td align="center" valign="middle" style="padding-bottom:40px" class="emailRegards">
                            <!-- Image and Link // -->
                            <a href="#" target="_blank" style="text-decoration:none;">
                                <img mc:edit="signature" src="https://pms.afifosh.com/assets/img/mail/signature.png" alt="" width="150" border="0" style="width:100%;
    max-width:150px; height:auto; display:block;">
                            </a>
                        </td>
    </tr>
              </tbody></table>
              <!-- Table Card Close// -->

              <!-- Space -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
                <tbody><tr>
                  <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>
              </tbody></table>

            </td>
          </tr>
        </tbody></table>'
        ]
      ],
      'verify_email' => [
        'subject' => 'Verify Email',
        'lang' => [
          'en' => '<table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
          <tbody><tr>
            <td align="center" valign="top">

              <!-- Table Card Open // -->
              <table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

                <tbody><tr>
                  <!-- Header Top Border // -->
                  <td height="3" style="background-color:#cd545b;font-size:1px;line-height:3px;" class="topBorder">&nbsp;</td>
                </tr>


                <tr>
                  <td align="center" valign="top" style="padding-bottom: 20px;" class="imgHero">
                    <!-- Hero Image // -->
                    <a href="#" target="_blank" style="text-decoration:none;">
                      <img src="https://pms.afifosh.com/assets/img/mail/user-account.png" width="600" alt="" border="0" style="width:100%; max-width:600px; height:auto; display:block;">
                    </a>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px;" class="mainTitle">
                    <!-- Main Title Text // -->
                    <h2 class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">
                      Hi "{user_name}"
                    </h2>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom:30px;padding-left:20px;padding-right:20px;" class="subTitle">
                    <!-- Sub Title Text // -->
                    <h4 class="text" style="color:#999999; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">
                      Verify Your Email Account
                    </h4>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-left:20px;padding-right:20px;" class="containtTable ui-sortable">

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDescription">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom:20px;" class="description">
                          <!-- Description Text// -->
                          <p class="text" style="color:#666666; font-family:\'Open Sans\', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            Thanks for siging up on {app_name}. Please click verify button to activate your account.
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableButton">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:20px;">

                          <!-- Button Table // -->
                          <table align="center" border="0" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                              <td align="center" class="ctaButton" style="background-color:#cd545b;padding-top:12px;padding-bottom:12px;padding-left:35px;padding-right:35px;border-radius:50px">
                                <!-- Button Link // -->
                                <a class="text" href="{verification_url}" target="_blank" style="color:#FFFFFF; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; font-style:normal;letter-spacing:1px; line-height:20px; text-transform:uppercase; text-decoration:none; display:block">Verify Email
                                </a>
                              </td>
                            </tr>
                          </tbody></table>

                        </td>
                      </tr>
                    </tbody></table>

                  </td>
                </tr>

                <tr>
                  <td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>

                <tr><td align="center" valign="middle" style="padding-bottom:40px" class="emailRegards">
                            <!-- Image and Link // -->
                            <a href="#" target="_blank" style="text-decoration:none;">
                                <img mc:edit="signature" src="https://pms.afifosh.com/assets/img/mail/signature.png" alt="" width="150" border="0" style="width:100%;
    max-width:150px; height:auto; display:block;">
                            </a>
                        </td>
    </tr>
              </tbody></table>
              <!-- Table Card Close// -->

              <!-- Space -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
                <tbody><tr>
                  <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>
              </tbody></table>

            </td>
          </tr>
        </tbody></table>'
        ]
      ],
      'failed_login' => [
        'subject' => 'Failed Login',
        'lang' => [
          'en' => 'test'
        ]
      ],
      'new_device_login' => [
        'subject' => 'New Device Login',
        'lang' => [
          'en' => 'test'
        ]
      ],
      'new_location_login' => [
        'subject' => 'New Location Login',
        'lang' => [
          'en' => 'test'
        ]
      ],
      'lost_recover_code' => [
        'subject' => 'Lost Recovery Code',
        'lang' => [
          'en' => 'test'
        ]
      ],
      'two_factor_code' => [
        'subject' => 'Two Factor Code',
        'lang' => [
          'en' => '<table border="0" cellpadding="0" cellspacing="0" style="max-width:600px;" width="100%" class="wrapperBody">
          <tbody><tr>
            <td align="center" valign="top">

              <!-- Table Card Open // -->
              <table border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;border-color:#E5E5E5; border-style:solid; border-width:0 1px 1px 1px;" width="100%" class="tableCard">

                <tbody><tr>
                  <!-- Header Top Border // -->
                  <td height="3" style="background-color:#cd545b;font-size:1px;line-height:3px;" class="topBorder">&nbsp;</td>
                </tr>


                <tr>
                  <td align="center" valign="top" style="padding-bottom: 20px;" class="imgHero">
                    <!-- Hero Image // -->
                    <a href="#" target="_blank" style="text-decoration:none;">
                      <img src="https://pms.afifosh.com/assets/img/mail/user-code.png" width="600" alt="" border="0" style="width:100%; max-width:600px; height:auto; display:block;">
                    </a>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom: 5px; padding-left: 20px; padding-right: 20px;" class="mainTitle">
                    <!-- Main Title Text // -->
                    <h2 class="text" style="color:#000000; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:28px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:36px; text-transform:none; text-align:center; padding:0; margin:0">Two Factor Code</h2>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-bottom: 30px; padding-left: 20px; padding-right: 20px;" class="subTitle">
                    <!-- Sub Title Text // -->
                    <h4 class="text" style="color:#999999; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:16px; font-weight:500; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">Please provide two factor code auth code to login.&nbsp;</h4>
                  </td>
                </tr>

                <tr>
                  <td align="center" valign="top" style="padding-left:20px;padding-right:20px;" class="containtTable ui-sortable">

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableMediumTitle" style="">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom: 20px;" class="mediumTitle">
                          <!-- Medium Title Text // -->
                          <p class="text" style="color:#3f4b97; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:34px; font-weight:300; font-style:normal; letter-spacing:normal; line-height:24px; text-transform:none; text-align:center; padding:0; margin:0">
                            USE CODE : {two_factore_code}</p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableDescription" style="">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-bottom: 20px;" class="description">
                          <!-- Description Text// -->
                          <p class="text" style="color:#666666; font-family:\'Open Sans\', Helvetica, Arial, sans-serif; font-size:14px; font-weight:400; font-style:normal; letter-spacing:normal; line-height:22px; text-transform:none; text-align:center; padding:0; margin:0">
                            Click on the button below to activate code, this is your requested account key code to log in with your email address ({user_email})
                          </p>
                        </td>
                      </tr>
                    </tbody></table>

                    <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tableButton" style="">
                      <tbody><tr>
                        <td align="center" valign="top" style="padding-top:20px;padding-bottom:20px;">

                          <!-- Button Table // -->
                          <table align="center" border="0" cellpadding="0" cellspacing="0">
                            <tbody><tr>
                              <td align="center" class="ctaButton" style="background-color: rgb(0, 60, 229); padding: 12px 35px; border-radius: 50px;">
                                <!-- Button Link // -->
                                <a class="text" href="#" target="_blank" style="color:#FFFFFF; font-family:\'Poppins\', Helvetica, Arial, sans-serif; font-size:13px; font-weight:600; font-style:normal;letter-spacing:1px; line-height:20px; text-transform:uppercase; text-decoration:none; display:block">
                                  Activate Code
                                </a>
                              </td>
                            </tr>
                          </tbody></table>

                        </td>
                      </tr>
                    </tbody></table>

                  </td>
                </tr>

                <tr>
                  <td height="20" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>

                <tr><td align="center" valign="middle" style="padding-bottom:40px" class="emailRegards">
                            <!-- Image and Link // -->
                            <a href="#" target="_blank" style="text-decoration:none;">
                                <img mc:edit="signature" src="https://pms.afifosh.com/assets/img/mail/signature.png" alt="" width="150" border="0" style="width:100%;
    max-width:150px; height:auto; display:block;">
                            </a>
                        </td>
    </tr>
              </tbody></table>
              <!-- Table Card Close// -->

              <!-- Space -->
              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
                <tbody><tr>
                  <td height="30" style="font-size:1px;line-height:1px;">&nbsp;</td>
                </tr>
              </tbody></table>

            </td>
          </tr>
        </tbody></table>'
        ]
      ],
    ];

    $email = EmailTemplate::all();

    foreach ($email as $e) {
      if (isset($defaultTemplate[$e->slug]))
        foreach ($defaultTemplate[$e->slug]['lang'] as $lang => $content) {
          $emailNoti = EmailTemplateLang::where('parent_id', $e->id)->where('lang', $lang)->count();
          if ($emailNoti == 0) {
            EmailTemplateLang::create(
              [
                'parent_id' => $e->id,
                'lang' => $lang,
                'subject' => $defaultTemplate[$e->slug]['subject'],
                'content' => $content,
              ]
            );
          }
        }
    }
  }
}
