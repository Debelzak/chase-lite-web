<?php
// General config
const DEBUG_MODE = true;
const ALLOW_NEW_USERS = true;
const DEFAULT_TIMEZONE = "America/New_York";
const TITLE = "Chase Lite";
const URL = "http://127.0.0.1:8000";
const ADMIN_URL = URL . "/admin";
const DOWNLOAD_SIZE = "5.31GB";
const FORUM = "https://forum.chaselite.net/";
const DISCORD = "https://discord.com/invite/D2pmTvMHzX";
const FACEBOOK = "#";
const TELEGRAM = "https://t.me/chaselite";

//Ranking configs
const RANKING_RESULTS_PER_PAGE = 10;
const RANKING_MAX_ALLOWED_PAGES = 10;

// reCAPTCHA config
const RECAPTCHA_ENABLE = true;
const RECAPTCHA_SECRET = "";
const RECAPTCHA_SITEKEY = "";

// Database config
const MSSQL_GAME    = ["host" => "192.168.1.100", "username" => "sa", "password" => "", "database" => "gc"];
const MSSQL_WEB     = ["host" => "192.168.1.100", "username" => "sa", "password" => "", "database" => "site"];

// Mailer config
const SMTP_HOST = "smtp.sendgrid.net";
const SMTP_PORT = 465;
const SMTP_USERNAME = "";
const SMTP_PASSWORD = "";
const SMTP_SENDERMAIL = "";
const SMTP_SENDERNAME = "Chase Lite Network";

// Donation config
const PAYPAL_USE_SANDBOX = false;
const PAYPAL_URL = (PAYPAL_USE_SANDBOX) ? "https://sandbox.paypal.com" : "https://www.paypal.com";
const PAYPAL_RETURN_URL = URL;
const PAYPAL_RECEIVE_EMAIL = "marciorusso26@gmail.com";
const PAYPAL_CURRENCY_TYPE = "USD";
const PAYPAL_NOTIFY_URL = URL . "/donate/notification";

const PAYMENT_TO_CASH = [
	"5" => "750",
	"10" => "1550",
	"20" => "3200",
	"40" => "6600",
	"80" => "13500",
	"100" => "17000",
];
