<?php
/**
 * Email template.
 *
 * @since {{VERSION}}
 *
 * @var array $email_parts All template parts to go in the email.
 */

defined( 'ABSPATH' ) || die();

$email_parts = $email_parts ? $email_parts : array(
	'logo',
	'heading',
	'message',
);

$wrapper_styles = array(
	'background' => '#f1f2f7',
	'padding' => '30px',
);

$container_styles = array(
	'background'    => '#fff',
	'padding'       => '4%',
	'border-radius' => '12px',
	'font-family'   => "'Arial','Helvetica','San-Serif'",
	'width'         => '92%',
	'max-width'     => '640px',
	'margin'        => '0 auto',
);


?>

<!DOCTYPE html>
<html
	xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width">
			<meta name="HandheldFriendly" content="true" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge" />
			<!--[if gte IE 7]>
			<html class="ie8plus"
				xmlns="http://www.w3.org/1999/xhtml">
				<![endif]-->
				<!--[if IEMobile]>
				<html class="ie8plus"
					xmlns="http://www.w3.org/1999/xhtml">
					<![endif]-->
					<meta name="format-detection" content="telephone=no">
						<meta name="generator" content="EDMdesigner, www.edmdesigner.com">
							<title>New Notification From YOOP</title>
							<!--##custom-font-resource##-->
							<style>
							<?php echo portal_build_style( $wrapper_styles ); ?>
							<?php echo portal_build_style( $container_styles ); ?>
							</style>
							<style type="text/css" media="screen">
               * {line-height: inherit;}
               .ExternalClass * { line-height: 100%; }
               body, p{margin:0; padding:0; margin-bottom:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none;} img{line-height:100%; outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;} a img{border: none;} a, a:link, .no-detect-local a, .appleLinks a{color:#5555ff !important; text-decoration: underline;}
							 a.button:hover, a.button:focus { background: #222; } a.button { padding: 10px 20px; color: white!important; background: #ffc900; text-decoration: none; border-radius: 8px; display: block; text-align: center; } .ExternalClass {display: block !important; width:100%;} .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: inherit; } table td {border-collapse:collapse;mso-table-lspace: 0pt; mso-table-rspace: 0pt;} sup{position: relative; top: 4px; line-height:7px !important;font-size:11px !important;} .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {text-decoration: default; color: #5555ff !important;
               pointer-events: auto; cursor: default;} .no-detect a{text-decoration: none; color: #5555ff; pointer-events: auto; cursor: default;} {color: #5555ff;} span {color: inherit; border-bottom: none;} span:hover { background-color: transparent; }
               .nounderline {text-decoration: none !important;}
               h1, h2, h3 { margin:0; padding:0; }
               p {Margin: 0px !important; }
               table[class="email-root-wrapper"] { width: 600px !important; }
               body {
               background-color: #e8e8e8;
               background: #e8e8e8;
               }
               body { min-width: 280px; width: 100%;}
               td[class="pattern"] .c56p10r { width: 10%;}
               td[class="pattern"] .c448p80r { width: 80%;}
            </style>
							<style>
               @media only screen and (max-width: 599px),
               only screen and (max-device-width: 599px),
               only screen and (max-width: 400px),
               only screen and (max-device-width: 400px) {
               .email-root-wrapper { width: 100% !important; }
               .full-width { width: 100% !important; height: auto !important; text-align:center;}
               .fullwidthhalfleft {width:100% !important;}
               .fullwidthhalfright {width:100% !important;}
               .fullwidthhalfinner {width:100% !important; margin: 0 auto !important; float: none !important; margin-left: auto !important; margin-right: auto !important; clear:both !important; }
               .hide { display:none !important; width:0px !important;height:0px !important; overflow:hidden; }
               .desktop-hide { display:block !important; width:100% !important;height:auto !important; overflow:hidden; max-height: inherit !important; }
               .c56p10r { width: 100% !important; float:none;}
               .c448p80r { width: 100% !important; float:none;}
               }
            </style>
							<style>
               @media only screen and (min-width: 600px) {
               td[class="pattern"] .c56p10r { width: 56px !important;}
               td[class="pattern"] .c448p80r { width: 448px !important;}
               }
               @media only screen and (max-width: 599px),
               only screen and (max-device-width: 599px),
               only screen and (max-width: 400px),
               only screen and (max-device-width: 400px) {
               table[class="email-root-wrapper"] { width: 100% !important; }
               td[class="wrap"] .full-width { width: 100% !important; height: auto !important;}
               td[class="wrap"] .fullwidthhalfleft {width:100% !important;}
               td[class="wrap"] .fullwidthhalfright {width:100% !important;}
               td[class="wrap"] .fullwidthhalfinner {width:100% !important; margin: 0 auto !important; float: none !important; margin-left: auto !important; margin-right: auto !important; clear:both !important; }
               td[class="wrap"] .hide { display:none !important; width:0px;height:0px; overflow:hidden; }
               td[class="pattern"] .c56p10r { width: 100% !important; }
               td[class="pattern"] .c448p80r { width: 100% !important; }
               }
            </style>
							<!--[if (gte IE 7) & (vml)]>
							<style type="text/css">
               html, body {margin:0 !important; padding:0px !important;}
               img.full-width { position: relative !important; }
               .img102x41 { width: 102px !important; height: 41px !important;}
            </style>
							<![endif]-->
							<!--[if gte mso 9]>
							<style type="text/css">
               .mso-font-fix-arial { font-family: Arial, sans-serif;}
               .mso-font-fix-georgia { font-family: Georgia, sans-serif;}
               .mso-font-fix-tahoma { font-family: Tahoma, sans-serif;}
               .mso-font-fix-times_new_roman { font-family: 'Times New Roman', sans-serif;}
               .mso-font-fix-trebuchet_ms { font-family: 'Trebuchet MS', sans-serif;}
               .mso-font-fix-verdana { font-family: Verdana, sans-serif;}
            </style>
							<![endif]-->
							<!--[if gte mso 9]>
							<style type="text/css">
               table, td {
               border-collapse: collapse !important;
               mso-table-lspace: 0px !important;
               mso-table-rspace: 0px !important;
               }
               .email-root-wrapper { width 600px !important;}
               .imglink { font-size: 0px; }
               .edm_button { font-size: 0px; }
            </style>
							<![endif]-->
							<!--[if gte mso 15]>
							<style type="text/css">
               table {
               font-size:0px;
               mso-margin-top-alt:0px;
               }
               .fullwidthhalfleft {
               width: 49% !important;
               float:left !important;
               }
               .fullwidthhalfright {
               width: 50% !important;
               float:right !important;
               }
            </style>
							<![endif]-->
							<STYLE type="text/css" media="(pointer) and (min-color-index:0)">
               html, body {background-image: none !important; background-color: transparent !important; margin:0 !important; padding:0 !important;}
            </STYLE>
						</head>
						<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="font-family:Arial, sans-serif; font-size:0px;margin:0;padding:0;background: #e8e8e8 !important;" bgcolor="#e8e8e8">
							<span style='display:none;font-size:0px;line-height:0px;max-height:0px;max-width:0px;opacity:0;overflow:hidden'>Preview Text</span>
							<!--[if t]>
							<![endif]-->
							<!--[if t]>
							<![endif]-->
							<!--[if t]>
							<![endif]-->
							<!--[if t]>
							<![endif]-->
							<!--[if t]>
							<![endif]-->
							<!--[if t]>
							<![endif]-->
							<table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%"  bgcolor="#e8e8e8" style="margin:0; padding:0; width:100% !important; background: #e8e8e8 !important;">
								<tr>
									<td class="wrap" align="center" valign="top" width="100%">
										<center>
											<!-- content -->
											<div  style="padding:0px">
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr>
														<td valign="top"  style="padding:0px">
															<table cellpadding="0" cellspacing="0" width="600" align="center"  style="max-width:600px;min-width:240px;margin:0 auto" class="email-root-wrapper">
																<tr>
																	<td valign="top"  style="padding:0px">
																		<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#ffca00"  style="border:0px none;background-color:#ffca00">
																			<tr>
																				<td valign="top"  style="padding:20px">
																					<table cellpadding="0" cellspacing="0" width="100%">
																						<tr>
																							<td  style="padding:0px">
																								<table cellpadding="0" cellspacing="0" width="100%">
																									<tr>
																										<td align="center"  style="padding:0px">
																											<table cellpadding="0" cellspacing="0" border="0" align="center" width="102" height="41"
      style="border:0px none;height:auto">
																												<tr>
																													<td valign="top"  style="padding:0px">
																														<svg width="102" height="41"
																															xmlns="http://www.w3.org/2000/svg"
																															xmlns:xlink="http://www.w3.org/1999/xlink">
																															<defs>
																																<path id="a" d="M0 0h101.274v40.038H0z"></path>
																															</defs>
																															<g fill="none" fill-rule="evenodd">
																																<mask id="b" fill="#fff">
																																	<use xlink:href="#a"></use>
																																</mask>
																																<path d="M99.975 13.987c0-6.497-4.925-12.843-13.18-12.843H76.024v38.894h4.924V26.783h5.847c8.255 0 13.18-6.295 13.18-12.796zm-4.07 14.51c-3.434 0-5.365 2.71-5.365 5.21s1.931 5.21 5.365 5.21c3.441 0 5.37-2.71 5.37-5.21s-1.929-5.21-5.37-5.21zM60.738.035c-7.866 0-12.783 5.312-14.036 11.035C45.446 5.347 40.527.035 32.66.035c-9.192 0-14.357 7.254-14.357 13.941 0 6.685 5.165 13.937 14.357 13.937 7.867 0 12.786-5.31 14.042-11.033 1.253 5.722 6.17 11.033 14.036 11.033 9.196 0 14.361-7.252 14.361-13.937 0-6.687-5.165-13.94-14.361-13.94zM18.489 0l-7.165 13.185L4.161 0H0l9.544 16.926V28.15h3.563V16.926L22.648 0h-4.159z" fill="#FFF" mask="url(#b)"></path>
																															</g>
																														</svg>
																													</td>
																												</tr>
																											</table>
																										</td>
																									</tr>
																								</table>
																								<table cellpadding="0" cellspacing="0" border="0" width="100%">
																									<tr>
																										<td valign="top"  style="padding:10px">
																											<div  style="text-align:left;font-family:Verdana, Geneva, sans-serif;font-size:16px;color:#ffffff;line-height:24px;mso-line-height:exactly;mso-text-raise:4px">
																												<h1 style="font-family:Verdana, Geneva, sans-serif; font-size: 30px; color: #ffffff; line-height: 42px; mso-line-height: exactly; mso-text-raise: 6px; padding: 0; margin: 0;text-align: center;">
																													<span class="mso-font-fix-verdana"></span>
																												</h1>
																												<h1 style="font-family:Verdana, Geneva, sans-serif; font-size: 30px; color: #ffffff; line-height: 42px; mso-line-height: exactly; mso-text-raise: 6px; padding: 0; margin: 0;text-align: center;">
																													<span class="mso-font-fix-verdana">Project Portal</span>
																												</h1>
																											</div>
																										</td>
																									</tr>
																								</table>
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr>
														<td valign="top"  style="padding:0px">
															<table cellpadding="0" cellspacing="0" width="600" align="center"  style="max-width:600px;min-width:240px;margin:0 auto" class="email-root-wrapper">
																<tr>
																	<td valign="top"  style="padding:0px">
																		<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#ffffff"  style="border:0px none;background-color:#ffffff">
																			<tr>
																				<td valign="top"  style="padding:20px">
																					<table cellpadding="0" cellspacing="0" width="100%">
																						<tr>
																							<td  style="padding:0px" class="pattern">
																								<table cellpadding="0" cellspacing="0" border="0" width="100%">
																									<tr>
																										<td valign="top"  style="padding:0;mso-cellspacing:0in">
																											<table cellpadding="0" cellspacing="0" border="0" align="left" width="56" id="c56p10r"  style="float:left"
      class="c56p10r">
																												<tr>
																													<td valign="top"  style="padding:0px">
																														<table cellpadding="0" cellspacing="0" border="0" width="100%">
																															<tr>
																																<td valign="top"  style="padding:2px">
																																	<table cellpadding="0" cellspacing="0" width="100%">
																																		<tr>
																																			<td  style="padding:0px">
																																				<table cellpadding="0" cellspacing="0" border="0" width="100%"  style="border-top:2px solid #ffffff">
																																					<tr>
																																						<td valign="top">
																																							<table cellpadding="0" cellspacing="0" width="100%">
																																								<tr>
																																									<td  style="padding:0px"></td>
																																								</tr>
																																							</table>
																																						</td>
																																					</tr>
																																				</table>
																																			</td>
																																		</tr>
																																	</table>
																																</td>
																															</tr>
																														</table>
																													</td>
																												</tr>
																											</table>
																											<!--[if gte mso 9]>
																										</td>
																										<td valign="top" style="padding:0;">
																											<![endif]-->

																											<?php

																												include_once('custom.php');

																											?>
                                                      <!-- body here -->



																											<!--[if gte mso 9]>
																										</td>
																										<td valign="top" style="padding:0;">
																											<![endif]-->
																											<table cellpadding="0" cellspacing="0" border="0" align="left" width="56" id="c56p10r"  style="float:left" class="c56p10r">
																												<tr>
																													<td valign="top"  style="padding:0px">
																														<table cellpadding="0" cellspacing="0" border="0" width="100%">
																															<tr>
																																<td valign="top"  style="padding:2px">
																																	<table cellpadding="0" cellspacing="0" width="100%">
																																		<tr>
																																			<td  style="padding:0px">
																																				<table cellpadding="0" cellspacing="0" border="0" width="100%"  style="border-top:2px solid #ffffff">
																																					<tr>
																																						<td valign="top">
																																							<table cellpadding="0" cellspacing="0" width="100%">
																																								<tr>
																																									<td  style="padding:0px"></td>
																																								</tr>
																																							</table>
																																						</td>
																																					</tr>
																																				</table>
																																			</td>
																																		</tr>
																																	</table>
																																</td>
																															</tr>
																														</table>
																													</td>
																												</tr>
																											</table>
																										</td>
																									</tr>
																								</table>
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr>
														<td valign="top"  style="padding:0px">
															<table cellpadding="0" cellspacing="0" width="600" align="center"  style="max-width:600px;min-width:240px;margin:0 auto" class="email-root-wrapper">
																<tr>
																	<td valign="top"  style="padding:0px">
																		<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#ffffff"  style="border:0px none;background-color:#ffffff">
																			<tr>
																				<td valign="top"  style="padding:20px">
																					<table cellpadding="0" cellspacing="0" width="100%">
																						<tr>
																							<td  style="padding:0px" class="pattern">
																								<table cellpadding="0" cellspacing="0" border="0" width="100%">
																									<tr>
																										<td valign="top"  style="padding:0;mso-cellspacing:0in">
																											<table cellpadding="0" cellspacing="0" border="0" align="left" width="56" id="c56p10r"  style="float:left"
      class="c56p10r">
																												<tr>
																													<td valign="top"  style="padding:0px">
																														<table cellpadding="0" cellspacing="0" border="0" width="100%">
																															<tr>
																																<td valign="top"  style="padding:2px">
																																	<table cellpadding="0" cellspacing="0" width="100%">
																																		<tr>
																																			<td  style="padding:0px">
																																				<table cellpadding="0" cellspacing="0" border="0" width="100%"  style="border-top:2px solid #ffffff">
																																					<tr>
																																						<td valign="top">
																																							<table cellpadding="0" cellspacing="0" width="100%">
																																								<tr>
																																									<td  style="padding:0px"></td>
																																								</tr>
																																							</table>
																																						</td>
																																					</tr>
																																				</table>
																																			</td>
																																		</tr>
																																	</table>
																																</td>
																															</tr>
																														</table>
																													</td>
																												</tr>
																											</table>
																											<!--[if gte mso 9]>
																										</td>
																										<td valign="top" style="padding:0;">
																											<![endif]-->
																											<table cellpadding="0" cellspacing="0" border="0" align="left" width="448" id="c448p80r"  style="float:left" class="c448p80r">
																												<tr>
																													<td valign="top"  style="padding:0px"></td>
																												</tr>
																											</table>
																											<!--[if gte mso 9]>
																										</td>
																										<td valign="top" style="padding:0;">
																											<![endif]-->
																											<table cellpadding="0" cellspacing="0" border="0" align="left" width="56" id="c56p10r"  style="float:left" class="c56p10r">
																												<tr>
																													<td valign="top"  style="padding:0px">
																														<table cellpadding="0" cellspacing="0" border="0" width="100%">
																															<tr>
																																<td valign="top"  style="padding:2px">
																																	<table cellpadding="0" cellspacing="0" width="100%">
																																		<tr>
																																			<td  style="padding:0px">
																																				<table cellpadding="0" cellspacing="0" border="0" width="100%"  style="border-top:2px solid #ffffff">
																																					<tr>
																																						<td valign="top">
																																							<table cellpadding="0" cellspacing="0" width="100%">
																																								<tr>
																																									<td  style="padding:0px"></td>
																																								</tr>
																																							</table>
																																						</td>
																																					</tr>
																																				</table>
																																			</td>
																																		</tr>
																																	</table>
																																</td>
																															</tr>
																														</table>
																													</td>
																												</tr>
																											</table>
																										</td>
																									</tr>
																								</table>
																							</td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												<table cellpadding="0" cellspacing="0" border="0" width="100%">
													<tr>
														<td valign="top"  style="padding:0px">
															<table cellpadding="0" cellspacing="0" width="600" align="center"  style="max-width:600px;min-width:240px;margin:0 auto" class="email-root-wrapper">
																<tr>
																	<td valign="top"  style="padding:0px">
																		<table cellpadding="0" cellspacing="0" border="0" width="100%">
																			<tr>
																				<td valign="top"  style="padding:10px">
																					<div  style="text-align:left;font-family:Verdana, Geneva, sans-serif;font-size:14px;color:#000000;line-height:20px;mso-line-height:exactly;mso-text-raise:3px">
																						<p style="padding: 0; margin: 0;text-align: center;">
																							<a href="http://yoopclub.co.uk/yoop/" style="color: #000000 !important; text-decoration: underline !important;" target="_blank">
																								<font style=" color:#000000;">YOOP Project Portal</font>
																							</a>
																						</p>
																						<p style="padding: 0; margin: 0;text-align: center;">&nbsp;</p>
																						<p style="padding: 0; margin: 0;text-align: center;">©YOOP Project Portal, All rights reserved. Office 128, 28A Church Road   start@yooparchitects.co.uk   02089546291</p>
																						<p
      style="padding: 0; margin: 0;text-align: center;"></p>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</div>
											<!-- content end -->
										</center>
									</td>
								</tr>
							</table>
						</body>
					</html>
