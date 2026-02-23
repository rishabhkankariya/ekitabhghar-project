<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once 'php/connection.php';
require_once 'admin/php/count.php';
require_once 'admin/php/fetch.php';
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="royalblue">
  <title>KITABGHAR | HOME</title>
  <link rel="apple-touch-icon" sizes="180x180" href="favicon_logoai/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="favicon_logoai/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="favicon_logoai/favicon-16x16.png">
  <link rel="manifest" href="favicon_logoai/site.webmanifest">


  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
    rel="stylesheet">

  <!-- SEO Meta Tags -->
  <meta name="description"
    content="Access a vast collection of Computer Science books, diploma CSE notes, and study materials. Access is restricted to students of the polytechnic college with a valid ID.">
  <meta name="keywords"
    content="CSE books, computer science notes, Ujjain Polytechnic CSE, RGPV diploma notes, 1st year CSE notes, 2nd year CSE notes, 3rd year CSE notes, RGPV polytechnic books, diploma CSE study materials, programming books for diploma, engineering books, RGPV syllabus notes, Ujjain Polytechnic diploma notes, best CSE books for students, RGPV diploma previous year papers, polytechnic computer science eBooks, CSE PDF notes download, programming notes, diploma CSE handwritten notes, computer science study resources">
  <meta name="author" content="Kitabghar">
  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="Kitabghar - CSE Books & Study Resources">
  <meta property="og:description"
    content="Download Computer Science books, programming guides, and study materials. Access is restricted to students of the polytechnic college with a valid ID.">
  <meta property="og:image" content="favicon_logoai/android-chrome-512x512.png">
  <meta property="og:type" content="website">
  <script src="https://kit.fontawesome.com/e72d27fd60.js" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js" defer></script>
  <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style type="text/css">
    /* 🎨 Professional White Scrollbar */
    ::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }

    ::-webkit-scrollbar-track {
      background: #0f172a;
      /* Slate-900 */
    }

    ::-webkit-scrollbar-thumb {
      background: #ffffff;
      border: 3px solid #0f172a;
      border-radius: 20px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: #e2e8f0;
    }

    /* 📣 Announcement Cards Specific Scrollbar */
    #announcement-cards::-webkit-scrollbar {
      height: 6px;
    }

    #announcement-cards::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 10px;
      margin: 0 10%;
    }

    #announcement-cards::-webkit-scrollbar-thumb {
      background: transparent;
      /* Transparent when not in use */
      border: none;
      border-radius: 10px;
    }

    #announcement-cards:hover::-webkit-scrollbar-thumb {
      background: #ffffff;
      /* Shows on hover */
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }

    #announcement-cards::-webkit-scrollbar-thumb:hover {
      background: #cbd5e1;
    }

    /* Firefox Support */
    * {
      scrollbar-width: thin;
      scrollbar-color: #ffffff #0f172a;
    }

    /* 🌐 Google Translate Customization */
    .goog-te-gadget {
      color: transparent !important;
      font-size: 0 !important;
      display: flex !important;
      align-items: center;
    }

    .goog-te-gadget .goog-te-combo {
      padding: 0.4rem 0.8rem;
      border-radius: 0.5rem;
      border: 1px solid #e2e8f0;
      background-color: #f8fafc;
      color: #334155;
      font-weight: 600;
      outline: none;
      cursor: pointer;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 0.875rem;
      transition: all 0.2s;
    }

    .goog-te-gadget .goog-te-combo:hover {
      border-color: #3b82f6;
      background-color: white;
    }

    /* Hide Google Branding */
    .goog-logo-link,
    .goog-te-gadget span {
      display: none !important;
    }

    .goog-te-banner-frame {
      display: none !important;
    }

    body {
      top: 0px !important;
    }

    /* 🌊 Marquee Animation - Slow & Single Pass */
    @keyframes marquee {
      0% {
        transform: translateX(100%);
      }

      100% {
        transform: translateX(-100%);
      }
    }

    .animate-marquee-slow {
      animation: marquee 25s linear infinite;
    }

    /* 📜 Custom Scrollbar Classes */
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
      background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #94a3b8;
    }

    .chatbot-scroll::-webkit-scrollbar {
      width: 4px;
    }

    .chatbot-scroll::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 4px;
    }


    /* 🎯 Fully Responsive Swiper */
    .swiper {
      width: 100%;
      height: 100vh;
      max-height: 500px;
      transition: filter 0.3s ease-in-out;
      border: 1px solid #004080;
    }

    /* 🖥️ Large Screen Fix (2560px+) */
    @media (min-width: 1440px) {
      .swiper {
        max-height: 85vh;
        /* Allows carousel to scale properly on large screens */
      }
    }

    /* 📌 Swiper Wrapper & Slide Styling */
    .swiper-wrapper {
      display: flex;
      width: 100%;
      height: 100%;
    }

    .swiper-slide {
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 100%;
    }

    .swiper-slide img {
      width: 100%;
      height: 100%;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
    }

    /* ⚙️ Custom Swiper Theme (Government Color Scheme) */
    .swiper {
      --swiper-theme-color: gold;
      /* Muted Gold */
      --swiper-pagination-bullet-size: 14px;
      --swiper-pagination-bullet-inactive-color: #ccc;
      --swiper-pagination-bullet-inactive-opacity: 0.7;
    }

    /* 🎯 Better Button Styling */
    .swiper-button-next,
    .swiper-button-prev {
      color: #f8f8f8 !important;
      /* White for visibility */
      text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
    }

    @media (max-width: 768px) {
      .carousel-header {
        font-size: 1.8rem;
      }

      .swiper {
        height: 50vh;
        /* Increased height for tablets */
      }

      .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
      }
    }

    .modal {
      position: fixed;
      inset: 0;
      /* Covers the entire screen */
      z-index: 999999;
      /* Highest z-index */
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: rgba(0, 0, 0, 0.4);
      /* Semi-transparent background */
    }

    .modal-content {
      background: linear-gradient(45deg, #1b263b, #00a8cc);
      padding: 20px;
      border: none;
      width: 80%;
      max-width: 500px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
      border-radius: 10px;
      color: white;
      text-align: center;
    }

    .announcement-list {
      list-style: none;
      padding: 0;
      margin-top: 15px;
    }

    .announcement-list li {
      background: rgba(255, 255, 255, 0.15);
      /* Subtle transparency */
      padding: 12px 15px;
      margin: 10px 0;
      border-radius: 8px;
      text-align: left;
      font-size: 15px;
      font-weight: 500;
      /* Balanced weight */
      display: flex;
      flex-direction: column;
      gap: 5px;
      transition: all 0.3s ease-in-out;
      position: relative;
      border-left: 4px solid #27ae60;
      /* Deep green accent */
    }

    .announcement-list li:hover {
      background: linear-gradient(45deg, rgba(41, 128, 185, 0.2), rgba(39, 174, 96, 0.2));
      transform: translateY(-2px);
      border-left: 4px solid #2980b9;
    }

    /* Title Styling */
    .announcement-list li strong {
      font-size: 17px;
      /* Slightly larger for emphasis */
      color: #e3e3e3;
      /* Soft light gray for readability */
      font-weight: 600;
      /* Medium-bold for a balanced look */
      letter-spacing: 0.5px;
      /* Adds a refined touch */
      text-transform: capitalize;
      /* Ensures a clean title format */
    }

    /* Bullet Indicator */
    .announcement-list li::before {
      content: "🔔";
      /* Notification bell */
      position: absolute;
      left: 0px;
      font-size: 12px;
      color: #27ae60;
      /* Deep green */
      animation: pulse 1.5s infinite alternate;
    }

    /* Subtle Pulsing Animation for Icon */
    @keyframes pulse {
      0% {
        transform: scale(1);
        opacity: 0.8;
      }

      100% {
        transform: scale(1.1);
        opacity: 1;
      }
    }

    .close-btn {
      background-color: darkslategrey;
      color: white;
      border: none;
      padding: 10px 20px;
      cursor: pointer;
      border-radius: 5px;
      font-size: 1rem;
      margin-top: 15px;
    }

    .close-btn:hover {
      background-color: #005f47;
    }

    @media (max-width: 1024px) {
      .swiper {
        height: 45vh;
        max-height: 500px;
      }
    }

    @media (max-width: 768px) {
      .swiper {
        height: 50vh;
        max-height: 600px;
      }

      .carousel-header {
        font-size: 1.8rem;
      }
    }

    @media (max-width: 480px) {
      .swiper {
        height: 60vh;
        /* Significantly increased for mobile */
        max-height: none;
        /* Removed restriction to allow full view */
      }

      .swiper-pagination-bullet {
        --swiper-pagination-bullet-size: 8px;
      }

      .carousel-header {
        font-size: 1.5rem;
      }
    }

    /* Scroll to Top Button */
    .scroll-top {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 50px;
      height: 50px;
      background: linear-gradient(145deg, #00b4db, #0083b0);
      color: white;
      border: none;
      border-radius: 50%;
      cursor: pointer;
      font-size: 20px;
      display: none;
      /* Initially hidden */
      justify-content: center;
      align-items: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease-in-out;
      z-index: 99999;
    }

    .scroll-top:hover {
      background: #ffcc00;
      color: #000;
    }

    /* Responsive: Adjust Size on Small Screens */
    @media screen and (max-width: 768px) {
      .scroll-top {
        width: 40px;
        height: 40px;
        font-size: 18px;
      }
    }

    /* Cookie Popup */
    .cookie-popup {
      position: fixed;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      width: 90%;
      max-width: 350px;
      background: #2C3E50;
      /* Modern dark blue */
      color: #fff;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
      text-align: center;
      font-size: 14px;
      display: none;
      opacity: 0;
      z-index: 9999;
      transition: opacity 0.5s ease-in-out;
    }

    .cookie-popup p {
      margin: 0;
      font-size: 13px;
      color: #ddd;
    }

    .cookie-popup a {
      color: #FFD700;
      /* Gold color for visibility */
      text-decoration: none;
      font-weight: bold;
    }

    .cookie-popup a:hover {
      text-decoration: underline;
    }

    .cookie-popup button {
      background: #27AE60;
      border: none;
      color: white;
      padding: 7px 14px;
      margin-top: 8px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 13px;
    }

    .cookie-popup button:hover {
      background: #219150;
    }

    /* Responsive for smaller screens */
    @media (max-width: 480px) {
      .cookie-popup {
        width: 95%;
        font-size: 12px;
      }
    }

    .section-title::after {
      display: none;
    }

    /* Study Section - Modern, Responsive & Well-Aligned */
    .ek-study-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      padding: 60px 8%;
      background: linear-gradient(135deg, #1E3C72, #2A5298);
      /* Cool Blue Gradient */
      color: #ffffff;
      position: relative;
      overflow: hidden;
      gap: 20px;
    }

    /* Subtle Floating Glow Effect */
    .ek-study-section::before {
      content: "";
      position: absolute;
      width: 180%;
      height: 180%;
      top: -50%;
      left: -40%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 70%);
      z-index: 0;
    }

    /* Left Side (Text Content) */
    .ek-study-text {
      max-width: 50%;
      position: relative;
      z-index: 2;
      text-align: left;
    }

    .ek-study-text h2 {
      font-size: 2.6rem;
      font-weight: 700;
      letter-spacing: 1px;
      margin-bottom: 15px;
      text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.3);
    }

    .ek-study-text p {
      font-size: 1.2rem;
      font-weight: 400;
      line-height: 1.6;
      margin-bottom: 20px;
      color: #f1f1f1;
    }

    /* Call-to-Action Button */
    .ek-btn {
      display: inline-block;
      padding: 14px 30px;
      font-size: 1.2rem;
      font-weight: 600;
      color: #fff;
      background: #FF5722;
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.3s ease-in-out;
      box-shadow: 0 4px 10px rgba(255, 87, 34, 0.4);
    }

    .ek-btn:hover {
      background: #E64A19;
      box-shadow: 0 6px 20px rgba(255, 87, 34, 0.6);
      transform: scale(1.05);
    }

    /* Right Side (Lottie Animation) */
    .ek-study-animation {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      z-index: 2;
      background-color: cornsilk;
      border-radius: 15px;
    }

    /* Fully Responsive Design */
    @media (max-width: 1024px) {
      .ek-study-section {
        flex-direction: column;
        text-align: center;
        padding: 50px 6%;
      }

      .ek-study-text,
      .ek-study-animation {
        max-width: 100%;
      }

      .ek-study-text h2 {
        font-size: 2.4rem;
      }

      .ek-study-text p {
        font-size: 1.1rem;
      }

      .ek-animation {
        width: 380px;
      }
    }

    @media (max-width: 768px) {
      .ek-study-section {
        padding: 40px 5%;
      }

      .ek-study-text h2 {
        font-size: 2rem;
      }

      .ek-study-text p {
        font-size: 1rem;
      }

      .ek-study-animation {
        display: none;
      }
    }

    .ek-study-section-2 {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      padding: 60px 8%;
      background: linear-gradient(135deg, #1E3C72, #2A5298);
      /* Cool Blue Gradient */
      color: #ffffff;
      position: relative;
      overflow: hidden;
      gap: 20px;
    }

    /* Subtle Floating Glow Effect */
    .ek-study-section-2::before {
      content: "";
      position: absolute;
      width: 180%;
      height: 180%;
      top: -50%;
      left: -40%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 10%, transparent 70%);
      z-index: 0;
    }

    /* Left Side (Text Content) */
    .ek-study-text-2 {
      max-width: 50%;
      position: relative;
      z-index: 2;
      text-align: left;
    }

    .ek-study-text-2 h2 {
      font-size: 2.6rem;
      font-weight: 700;
      letter-spacing: 1px;
      margin-bottom: 15px;
      text-shadow: 2px 2px 12px rgba(0, 0, 0, 0.3);
    }

    .ek-study-text-2 p {
      font-size: 1.2rem;
      font-weight: 400;
      line-height: 1.6;
      margin-bottom: 20px;
      color: #f1f1f1;
    }

    /* Call-to-Action Button */
    .ek-btn-2 {
      display: inline-block;
      padding: 14px 30px;
      font-size: 1.2rem;
      font-weight: 600;
      color: #fff;
      background: #FF5722;
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.3s ease-in-out;
      box-shadow: 0 4px 10px rgba(255, 87, 34, 0.4);
    }

    .ek-btn-2:hover {
      background: #E64A19;
      box-shadow: 0 6px 20px rgba(255, 87, 34, 0.6);
      transform: scale(1.05);
    }

    /* Right Side (Lottie Animation) */
    .ek-study-animation-2 {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      z-index: 2;
      background-color: cornsilk;
      border-radius: 15px;
    }

    /* Fully Responsive Design */
    @media (max-width: 1024px) {
      .ek-study-section-2 {
        flex-direction: column;
        text-align: center;
        padding: 50px 6%;
      }

      .ek-study-text-2,
      .ek-study-animation-2 {
        max-width: 100%;
      }

      .ek-study-text-2 h2 {
        font-size: 2.4rem;
      }

      .ek-study-text-2 p {
        font-size: 1.1rem;
      }

      .ek-animation-2 {
        width: 380px;
      }
    }

    @media (max-width: 768px) {
      .ek-study-section-2 {
        padding: 40px 5%;
      }

      .ek-study-text-2 h2 {
        font-size: 2rem;
      }

      .ek-study-text-2 p {
        font-size: 1rem;
      }

      .ek-study-animation-2 {
        display: none;
      }
    }

    /* Sticky Marquee */
    .sticky-marquee {
      position: fixed;
      top: -60px;
      /* Initially hidden */
      left: 0;
      width: 100%;
      background: linear-gradient(to right, #00b4db, #0083b0);
      /* Matching Theme */
      color: #fff;
      font-size: 1.2rem;
      font-weight: bold;
      padding: 2px 0;
      text-align: center;
      transition: top 0.5s ease-in-out;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      white-space: nowrap;
      overflow: hidden;
    }

    /* Marquee Text Styling */
    .marquee-text {
      display: inline-block;
      animation: marquee 16s linear infinite;
      /* Marquee effect */
      font-size: 1.2rem;
      letter-spacing: 1px;
    }

    /* Keyframes for marquee animation */
    @keyframes marquee {
      0% {
        transform: translateX(100%);
        /* Start from the right */
      }

      100% {
        transform: translateX(-100%);
        /* Move to the left */
      }
    }

    /* Sticky Marquee Visible */
    .show-marquee {
      top: 0;
    }

    /* Marquee Hover to Stop */
    .sticky-marquee:hover .marquee-text {
      animation-play-state: paused;
      /* Pause the animation on hover */
    }


    .donation-section {
      width: 100%;
      background: linear-gradient(to bottom, #004D40, #00796B);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px 20px;
    }

    .donation-container {
      max-width: 1200px;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
    }

    .donation-left {
      width: 50%;
      text-align: left;
      padding: 40px;
    }

    .donation-left h2 {
      font-size: 36px;
      margin-bottom: 15px;
      color: #FFD369;
      /* Warm Gold */
      font-weight: bold;
      transition: 0.3s ease-in-out;
    }

    .donation-left p {
      font-size: 20px;
      margin-bottom: 20px;
      color: #EEEEEE;
      line-height: 1.6;
    }

    .donate-btn {
      background: #ff8800;
      color: white;
      padding: 15px 30px;
      border: none;
      font-size: 20px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 10px;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease-in-out;
    }

    .donate-btn i {
      font-size: 24px;
    }

    .donate-btn:hover {
      background: #cc6600;
      transform: scale(1.05);
      font-size: 20px;
      box-shadow: 0px 0px 20px rgba(255, 211, 105, 0.5);
    }

    .donation-right {
      width: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .donation-right img {
      width: 280px;
      height: 480px;
      background: white;
      padding: 10px;
      transition: transform 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
    }

    .donation-right img:hover {
      transform: translateY(-5px);
      box-shadow: 0px 0px 20px rgba(255, 211, 105, 0.5);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .donation-container {
        flex-direction: column;
        text-align: center;
      }

      .donation-left,
      .donation-right {
        width: 100%;
        padding: 30px;
      }

      .donation-right img {
        width: 220px;
        height: 480px;
      }
    }

    .ek-navbar {
      position: relative;
      padding: 10px 20px;
      overflow: hidden;
      z-index: 10;
    }

    /* ⚡ Ultra-precise animated lines */
    .ek-navbar::before {
      content: '';
      position: absolute;
      top: -100%;
      left: -100%;
      width: 300%;
      height: 300%;
      background: repeating-linear-gradient(135deg,
          rgba(255, 255, 255, 0.08),
          rgba(255, 255, 255, 0.05) 3px,
          transparent 5px,
          transparent 35px);
      animation: animateLines 8s linear infinite;
      filter: drop-shadow(0 0 6px rgba(255, 255, 255, 0.3));
      z-index: -1;
      pointer-events: none;
    }

    /* ✨ Clean glowing edge with pulse */
    .ek-navbar::after {
      content: '';
      position: absolute;
      inset: 0;
      border: 4px solid rgba(255, 255, 255, 0.08);
      box-shadow:
        0 0 15px rgba(255, 255, 255, 0.15),
        inset 0 0 20px rgba(255, 255, 255, 0.05);
      animation: edgeGlow 5s ease-in-out infinite;
      z-index: -1;
      pointer-events: none;
    }

    /* ⏱ Smooth infinite motion */
    @keyframes animateLines {
      0% {
        background-position: 0 0;
      }

      100% {
        background-position: 120px 120px;
      }
    }

    /* ✴️ Gentle pulse for outer border */
    @keyframes edgeGlow {

      0%,
      100% {
        box-shadow:
          0 0 15px rgba(255, 255, 255, 0.15),
          inset 0 0 20px rgba(255, 255, 255, 0.05);
      }

      50% {
        box-shadow:
          0 0 30px rgba(255, 255, 255, 0.25),
          inset 0 0 30px rgba(255, 255, 255, 0.1);
      }
    }

    /* Section Styling */
    .quick-links-section {
      padding: 40px 20px;
      background-color: #e6f0ff;
      text-align: center;
    }

    .section-title {
      font-family: 'Poppins', sans-serif;
      font-size: 28px;
      font-weight: 600;
      color: #222;
      margin-bottom: 30px;
    }

    .quick-links-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      justify-content: center;
      max-width: 1100px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Force 5 columns around 1024px to avoid 4 top + 1 bottom weirdness */
    @media screen and (min-width: 992px) and (max-width: 1150px) {
      .quick-links-grid {
        grid-template-columns: repeat(5, 1fr);
      }
    }

    @media screen and (max-width: 960px) {
      .quick-links-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media screen and (max-width: 768px) {
      .quick-links-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media screen and (max-width: 600px) {
      .quick-links-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media screen and (max-width: 480px) {
      .quick-links-grid {
        grid-template-columns: repeat(1, 1fr);
      }
    }

    /* Card Base */
    .quick-card {
      display: block;
      position: relative;
      border-radius: 8px;
      overflow: hidden;
      text-decoration: none;
      transition: transform 0.3s ease;
      height: 180px;
      /* Maintain fixed height */
    }

    .quick-card:hover {
      transform: scale(1.05);
    }

    /* Image & Overlay */
    .card-img-wrapper {
      position: relative;
      width: 100%;
      height: 100%;
      overflow: hidden;
    }

    .card-img-wrapper img {
      width: 100%;
      height: 100%;
      transition: transform 0.4s ease;
    }

    .quick-card:hover img {
      transform: scale(1.15);
    }

    /* Top Right Icon */
    .top-icon {
      position: absolute;
      top: 8px;
      right: 8px;
      color: #fff;
      font-size: 16px;
      background-color: rgba(0, 0, 0, 0.5);
      padding: 6px;
      border-radius: 5px;
      z-index: 2;
    }

    /* Hover Overlay */
    .hover-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: all 0.3s ease;
      color: #fff;
      padding: 10px;
      text-align: center;
    }

    .quick-card:hover .hover-overlay {
      opacity: 1;
    }

    .hover-title {
      font-family: 'Poppins', sans-serif;
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .hover-btn {
      padding: 8px 16px;
      font-size: 12px;
      border: none;
      background-color: #fff;
      color: #000;
      border-radius: 4px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .hover-btn:hover {
      background-color: #ddd;
    }

    /* Responsive Adjustments */
    @media screen and (max-width: 600px) {
      .quick-card {
        height: 200px;
      }

      .hover-title {
        font-size: 14px;
      }

      .hover-btn {
        font-size: 11px;
        padding: 6px 12px;
      }
    }

    @keyframes marquee-right {
      0% {
        transform: translateX(-100%);
      }

      100% {
        transform: translateX(100%);
      }
    }

    .animate-marquee-right {
      animation: marquee-right 35s linear infinite;
      will-change: transform;
    }

    /* Pause the animation on hover */
    .marquee-container:hover .animate-marquee-right {
      animation-play-state: paused;
    }

    /* Fix Google Translate widget styles */
    .notailwind-translate * {
      all: unset !important;
    }

    .notailwind-translate select,
    .notailwind-translate iframe,
    .notailwind-translate div {
      all: unset !important;
      box-sizing: content-box !important;
    }

    .notailwind-translate {
      z-index: 9999;
      position: relative;
      width: auto;
      max-width: 200px;
    }

    /* Toast Slide-In Animation */
    @keyframes toastSlideUp {
      0% {
        opacity: 0;
        transform: translateY(40px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .toast-show {
      animation: toastSlideUp 0.7s ease-out forwards;
    }

    /* Initially hide developer info on small screens */
    .developer-info {
      display: none;
    }

    /* Show developer info on screens larger than 768px */
    @media (min-width: 768px) {
      .developer-info {
        display: block;
      }
    }

    /* AOS animations */
    .developer-info {
      opacity: 0;
      animation: fadeInUp 1s forwards;
    }

    /* Keyframe animation for fade-in and slide-up effect */
    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }

      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Hover effect on anchor links */
    .developer-info a:hover {
      color: #007BFF;
      /* Blue color on hover */
      text-decoration: underline;
      /* Underline effect on hover */
      transition: all 0.3s ease;
    }

    /* Parallax Section with Unique Class Names */
    .contribution-section-parallax {
      position: relative;
      min-height: 100vh;
      /* Full screen height */
      background-image: url('img/section.jpg');
      /* Your background image */
      background-attachment: fixed;
      /* Parallax effect */
      background-position: center;
      background-size: cover;
      color: white;
      transition: background-position 0.2s ease-out;
    }

    .parallax-background-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      /* Overlay to darken background */
      z-index: 1;
    }

    .parallax-section-content {
      position: relative;
      z-index: 2;
      /* Make content above the overlay */
      text-align: center;
      padding: 100px 20px;
    }

    .parallax-section-title {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.6);
    }

    .parallax-section-description {
      font-size: 1.25rem;
      margin-bottom: 40px;
      max-width: 800px;
      margin: 0 auto;
    }

    /* Transparent Form for Notes Submission */
    .parallax-form-wrapper {
      max-width: 400px;
      margin: 0 auto;
      padding: 30px;
      background-color: rgba(255, 255, 255, 0.3);
      /* Transparent background */
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(10px);
      /* Slight blur effect */
    }

    .parallax-form-label {
      display: block;
      font-size: 1rem;
      margin-bottom: 5px;
      color: white;
    }

    .parallax-form-input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid rgba(255, 255, 255, 0.4);
      border-radius: 5px;
      background-color: rgba(255, 255, 255, 0.5);
      color: black;
    }

    .parallax-submit-btn {
      padding: 10px 20px;
      background-color: #4C6EF5;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .parallax-submit-btn:hover {
      background-color: #5a80f0;
    }

    /* Hide the scrollbar */
    .scrollbar-hide::-webkit-scrollbar {
      display: none;
    }

    .scrollbar-hide {
      -ms-overflow-style: none;
      /* IE and Edge */
      scrollbar-width: none;
      /* Firefox */
    }
  </style>
</head>

<body>
  <!-- 🌊 Modern Loader -->
  <div id="loader"
    class="fixed inset-0 z-[10000] bg-slate-900 flex flex-col items-center justify-center transition-opacity duration-500">
    <div class="relative w-32 h-32 mb-8">
      <div class="absolute inset-0 border-t-4 border-blue-500 border-solid rounded-full animate-spin"></div>
      <div class="absolute inset-2 border-t-4 border-cyan-400 border-solid rounded-full animate-spin_reverse"></div>
      <div class="absolute inset-0 flex items-center justify-center">
        <img src="favicon_logoai/apple-touch-icon.png" alt="Logo" class="w-16 h-16 object-contain animate-pulse">
      </div>
    </div>
    <div class="flex gap-2">
      <div class="w-3 h-3 bg-blue-500 rounded-full animate-bounce delay-75"></div>
      <div class="w-3 h-3 bg-cyan-500 rounded-full animate-bounce delay-150"></div>
      <div class="w-3 h-3 bg-indigo-500 rounded-full animate-bounce delay-300"></div>
    </div>
  </div>

  <!-- 📢 Modern Announcement Modal -->
  <div id="announcementModal"
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300"
    role="dialog" aria-modal="true">
    <div
      class="bg-white dark:bg-slate-800 w-full max-w-lg mx-4 rounded-3xl shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-200 dark:border-slate-700">
      <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
          <i class="bi bi-bell-fill animate-swing"></i> Latest Updates
        </h2>
        <button id="closeModal" class="text-white/80 hover:text-white transition-colors">
          <i class="bi bi-x-lg text-xl"></i>
        </button>
      </div>
      <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
        <ul class="space-y-4">
          <?php if (!empty($modal_announcements)): ?>
            <?php foreach ($modal_announcements as $item): ?>
              <li
                class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl border-l-4 border-blue-500 hover:bg-blue-50 dark:hover:bg-slate-700 transition-colors">
                <strong
                  class="block text-slate-800 dark:text-white mb-1 text-lg"><?php echo htmlspecialchars($item['title']); ?></strong>
                <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
                  <?php echo htmlspecialchars($item['message']); ?>
                </p>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li class="text-center text-slate-500 py-4">No new announcements.</li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="p-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 text-right">
        <button id="closeModalBtn"
          class="px-6 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white rounded-lg font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors">
          Dismiss
        </button>
      </div>
    </div>
  </div>

  <!-- 🍪 Modern Cookie Consent -->
  <div id="cookiePopup"
    class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-md bg-slate-900/90 backdrop-blur-md text-white p-6 rounded-2xl shadow-2xl z-[9000] hidden border border-slate-700">
    <div class="flex items-start gap-4">
      <div class="text-3xl">🍪</div>
      <div class="flex-1">
        <p class="text-sm text-slate-300 mb-4 leading-relaxed">
          We use cookies to ensure you get the best experience on our website.
          <a href="cookie-policy.html" class="text-blue-400 hover:text-blue-300 underline underline-offset-2">Learn
            more</a>
        </p>
        <button onclick="acceptCookies()"
          class="w-full sm:w-auto px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold transition-all shadow-lg shadow-blue-600/20">
          Accept & Continue
        </button>
      </div>
    </div>
  </div>

  <!-- 🔝 Scroll to Top -->
  <button id="scrollTopBtn"
    class="fixed bottom-8 right-4 w-12 h-12 bg-blue-600/80 hover:bg-blue-700 backdrop-blur-sm text-white rounded-full shadow-lg shadow-blue-600/30 z-[50] hidden items-center justify-center transition-all hover:scale-110 active:scale-95 group opacity-70 hover:opacity-100">
    <i class="fa-solid fa-arrow-up group-hover:-translate-y-1 transition-transform"></i>
  </button>

  <!-- ================= HEADER SECTION ================= -->

  <!-- 🟢 Top Bar -->
  <div class="bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 py-2 hidden md:block">
    <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-sm">
      <!-- Left: Translate -->
      <div class="translate-wrapper relative z-50">
        <div id="google_translate_element"></div>
      </div>

      <!-- Right: Interactions -->
      <div class="flex items-center gap-6">
        <!-- Font Size -->
        <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-lg p-1">
          <button id="decrease-font"
            class="w-8 h-8 flex items-center justify-center hover:bg-white dark:hover:bg-slate-700 rounded-md transition-colors text-slate-600 dark:text-slate-300"
            title="Decrease Size">A-</button>
          <button id="reset-font"
            class="w-8 h-8 flex items-center justify-center hover:bg-white dark:hover:bg-slate-700 rounded-md transition-colors text-slate-600 dark:text-slate-300 border-x border-slate-200 dark:border-slate-700"
            title="Reset Size"><i class="fa-solid fa-sync"></i></button>
          <button id="increase-font"
            class="w-8 h-8 flex items-center justify-center hover:bg-white dark:hover:bg-slate-700 rounded-md transition-colors text-slate-600 dark:text-slate-300"
            title="Increase Size">A+</button>
        </div>

        <div class="flex items-center gap-3 text-slate-500 dark:text-slate-400">
          <a href="https://www.instagram.com/ekitabghar/"
            class="inline-block transition duration-300 hover:bg-gradient-to-r hover:from-[#833ab4] hover:via-[#fd1d1d] hover:to-[#fcb045] hover:bg-clip-text hover:text-transparent [-webkit-background-clip:text]">
            <i class="fa-brands fa-instagram text-lg"></i>
          </a>
          <a href="#" class="hover:text-blue-600 transition-colors"><i class="fa-brands fa-facebook text-lg"></i></a>
          <a href="https://www.whatsapp.com/channel/your-channel" class="hover:text-green-500 transition-colors"><i
              class="fa-brands fa-whatsapp text-lg"></i></a>
          <a href="#" class="hover:text-blue-500 transition-colors"><i class="fa-brands fa-telegram text-lg"></i></a>
          <a href="#" class="hover:text-red-600 transition-colors"><i class="fa-brands fa-youtube text-lg"></i></a>
        </div>
        <!-- Contact -->
        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-300 font-medium">
          <a href="mailto:ekitabghar@gmail.com" class="flex items-center gap-2 hover:text-blue-600"><i
              class="fa-solid fa-envelope text-blue-500"></i> ekitabghar[at]gmail[dot]com</a>
          <a href="tel:+917697164221" class="flex items-center gap-2 hover:text-blue-600"><i
              class="fa-solid fa-phone text-blue-500"></i> +91 7697164221</a>
        </div>
      </div>
    </div>
  </div>

  <!-- 🏛️ Main Branding Header -->
  <header class="bg-white dark:bg-slate-900 shadow-sm relative z-40">
    <div class="max-w-7xl mx-auto px-4 py-4 md:py-6 flex flex-col md:flex-row items-center gap-4 md:gap-8">
      <!-- Logo -->
      <div class="flex items-center gap-4 flex-shrink-0 animate-fade-in-down">
        <img loading="lazy" src="favicon_logoai/apple-touch-icon.png" style="width: 30%;" alt="Govt Logo"
          class="h-20 w-25 md:h-20 md:w-20 object-cover rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.12)] ring-4 ring-white/80 dark:ring-white/10 hover:shadow-[0_20px_50px_rgba(8,_112,_184,_0.3)] hover:scale-105 active:scale-95 transition-all duration-500 ease-out bg-white cursor-pointer backdrop-blur-sm">
        <div class="h-12 w-px bg-slate-200 dark:bg-slate-700 hidden md:block"></div>
        <div class="text-center md:text-left">
          <h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Kitabghar</h1>
          <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400 font-medium tracking-wide uppercase">Govt
            Polytechnic College Ujjain</p>
        </div>
      </div>

      <!-- Descriptions -->
      <div class="hidden md:block flex-1 text-center md:text-right">
        <p class="text-white text-sm leading-relaxed max-w-xl ml-auto">
          An online platform for <strong class="text-blue-600">Computer Science</strong> diploma students.
          Access <strong class="text-slate-800 dark:text-slate-200">notes, books, and resources</strong> to accelerate
          your academic journey.
        </p>
      </div>
    </div>
  </header>

  <!-- 🧭 Navbar -->
  <nav
    class="sticky top-0 z-[100] bg-white/90 dark:bg-slate-900/90 backdrop-blur-lg border-b border-slate-200 dark:border-slate-800 shadow-md transition-all duration-300"
    id="mainNavbar">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex items-center justify-between h-16 md:h-20">

        <!-- Mobile Menu Triggers -->
        <div class="flex items-center gap-4 md:hidden">
          <button onclick="toggleSidebar()"
            class="p-2 text-slate-700 dark:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
            <i class="fa-solid fa-bars text-2xl"></i>
          </button>
          <img src="favicon_logoai/apple-touch-icon.png" alt="Logo" class="h-10 rounded-lg">
        </div>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-1">
          <a href="index.php"
            class="px-4 py-2 rounded-lg text-blue-600 bg-blue-50 dark:bg-blue-900/20 font-bold flex items-center gap-2">
            <i class="fa-solid fa-house"></i> Home
          </a>
          <a href="about.html"
            class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-2">
            <i class="bi bi-bookmark-star-fill"></i> About
          </a>

          <!-- Dropdown -->
          <div class="relative group">
            <button
              class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-2 group">
              <i class="bi bi-journal-bookmark-fill"></i> CSE Notes <i
                class="fa-solid fa-chevron-down text-xs transition-transform group-hover:rotate-180"></i>
            </button>
            <div
              class="absolute top-full left-0 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 p-2">
              <a href="notes/firstsem.html"
                class="block px-4 py-3 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium transition-colors">1st
                Semester</a>
              <a href="notes/secondsem.html"
                class="block px-4 py-3 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium transition-colors">2nd
                Semester</a>
              <a href="notes/thirdsem.html"
                class="block px-4 py-3 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium transition-colors">3rd
                Semester</a>
              <a href="notes/fourthsem.html"
                class="block px-4 py-3 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium transition-colors">4th
                Semester</a>
              <a href="notes/fifthsem.html"
                class="block px-4 py-3 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium transition-colors">5th
                Semester</a>
              <a href="notes/sixthsem.html"
                class="block px-4 py-3 rounded-lg hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-sm font-medium transition-colors">6th
                Semester</a>
            </div>
          </div>

          <a href="syllabus.html"
            class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-2">
            <i class="bi bi-stickies-fill"></i> Syllabus
          </a>
          <a href="question.html"
            class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-2">
            <i class="bi bi-stickies-fill"></i> Papers
          </a>
          <a href="library/index.php"
            class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:text-blue-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all flex items-center gap-2">
            <i class="bi bi-book-half"></i> Library
          </a>
        </div>

        <!-- Right: Developers & Login -->
        <div class="hidden md:flex items-center gap-3">
          <!-- Developer 1 Group -->
          <div class="relative group hidden lg:flex">
            <div class="flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-slate-700 cursor-pointer">
              <img src="img/rishabh.png" alt="Rishabh"
                class="w-10 h-10 rounded-full object-cover ring-2 ring-transparent group-hover:ring-blue-500 transition-all">
              <div class="text-left">
                <p class="text-xs font-bold text-slate-900 dark:text-white">Rishabh K.</p>
                <p class="text-[10px] text-blue-600 font-semibold tracking-wide">DEVELOPER</p>
              </div>
            </div>
            <!-- Hover Card -->
            <div
              class="absolute top-full right-0 mt-4 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-4 border border-slate-100 dark:border-slate-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
              <div class="flex flex-col items-center text-center">
                <img src="img/rishabh.png" class="w-20 h-20 rounded-full object-cover mb-3 shadow-md">
                <h4 class="font-bold text-slate-900 dark:text-white">Rishabh Kankariya</h4>
                <p class="text-xs text-slate-500 mb-4">Full Stack Developer</p>
                <div class="flex gap-2 w-full mb-2">
                  <a href="mailto:rishabhkankariya69@gmail.com"
                    class="flex-1 py-2 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold hover:bg-blue-100 transition-colors"><i
                      class="bi bi-envelope-fill"></i> Email</a>
                  <a href="tel:+917014834315"
                    class="flex-1 py-2 bg-green-50 text-green-600 rounded-lg text-[10px] font-bold hover:bg-green-100 transition-colors"><i
                      class="bi bi-telephone-fill"></i> Call</a>
                </div>
                <a href="https://www.linkedin.com/in/rishabh-kankariya-202a93252/" target="_blank"
                  class="w-full py-2 bg-slate-900 text-white rounded-lg text-[10px] font-bold hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                  <i class="bi bi-linkedin"></i> LinkedIn Profile
                </a>
              </div>
            </div>
          </div>

          <!-- Developer 2 Group -->
          <div class="relative group hidden lg:flex">
            <div class="flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-slate-700 cursor-pointer">
              <img src="img/gourav.jpeg" alt="Gourav"
                class="w-10 h-10 rounded-full object-cover ring-2 ring-transparent group-hover:ring-purple-500 transition-all">
              <div class="text-left">
                <p class="text-xs font-bold text-slate-900 dark:text-white">Gourav S.</p>
                <p class="text-[10px] text-purple-600 font-semibold tracking-wide">DEVELOPER</p>
              </div>
            </div>
            <!-- Hover Card -->
            <div
              class="absolute top-full right-0 mt-4 w-64 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-4 border border-slate-100 dark:border-slate-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
              <div class="flex flex-col items-center text-center">
                <img src="img/gourav.jpeg" class="w-20 h-20 rounded-full object-cover mb-3 shadow-md">
                <h4 class="font-bold text-slate-900 dark:text-white">Gourav Sen</h4>
                <p class="text-xs text-slate-500 mb-4">Full Stack Developer</p>
                <div class="flex gap-2 w-full mb-2">
                  <a href="mailto:gsen5448@gmail.com"
                    class="flex-1 py-2 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold hover:bg-blue-100 transition-colors"><i
                      class="bi bi-envelope-fill"></i> Email</a>
                  <a href="tel:+917772834736"
                    class="flex-1 py-2 bg-green-50 text-green-600 rounded-lg text-[10px] font-bold hover:bg-green-100 transition-colors"><i
                      class="bi bi-telephone-fill"></i> Call</a>
                </div>
                <a href="https://www.linkedin.com/in/gourav-sen-614b62375/" target="_blank"
                  class="w-full py-2 bg-slate-900 text-white rounded-lg text-[10px] font-bold hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                  <i class="bi bi-linkedin"></i> LinkedIn Profile
                </a>
              </div>
            </div>
          </div>

          <!-- Login Button -->
          <div class="relative group ml-2">
            <button
              class="px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold text-sm hover:shadow-lg transition-all transform group-hover:scale-105 flex items-center gap-2">
              <i class="bi bi-person-circle"></i> Login
            </button>
            <div
              class="absolute top-full right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-100 dark:border-slate-700 overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
              <a href="student_login.html"
                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200">
                <i class="bi bi-mortarboard-fill text-blue-500 mr-2"></i> Student Login
              </a>
              <a href="admin/admin_login.php"
                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 border-t border-slate-100 dark:border-slate-700">
                <i class="bi bi-shield-lock-fill text-red-500 mr-2"></i> Admin Login
              </a>
              <a href="library_login.html"
                class="block px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 border-t border-slate-100 dark:border-slate-700">
                <i class="bi bi-book-fill text-yellow-500 mr-2"></i> Library Login
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </nav>

  <!-- 📱 Modern Off-Canvas Sidebar -->
  <div id="sidebar"
    class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-slate-900 shadow-2xl z-[999] transform -translate-x-full transition-transform duration-300 ease-out flex flex-col h-full">
    <div
      class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-950">
      <img src="img/kitabghar.png" alt="Logo" class="h-10 rounded-lg shadow-sm">
      <button onclick="toggleSidebar()"
        class="w-8 h-8 flex items-center justify-center bg-white dark:bg-slate-800 rounded-full shadow-sm text-slate-500 hover:text-red-500 transition-colors">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="flex-1 overflow-y-auto p-4 space-y-1 custom-scrollbar">
      <a href="index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 text-blue-600 font-semibold mb-2">
        <i class="fa-solid fa-house"></i> Home
      </a>
      <a href="about.html"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium transition-colors">
        <i class="bi bi-bookmark-star-fill text-slate-400"></i> About Us
      </a>

      <!-- Sidebar Dropdown -->
      <div class="relative">
        <button
          onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.arrow').classList.toggle('rotate-180')"
          class="w-full flex items-center justify-between px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium transition-colors">
          <span class="flex items-center gap-3"><i class="bi bi-journal-bookmark-fill text-slate-400"></i> CSE
            Notes</span>
          <i class="fa-solid fa-chevron-down text-xs arrow transition-transform duration-300"></i>
        </button>
        <div class="hidden pl-11 pr-2 space-y-1 py-1">
          <a href="notes/firstsem.html"
            class="block py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600">1st Semester</a>
          <a href="notes/secondsem.html"
            class="block py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600">2nd Semester</a>
          <a href="notes/thirdsem.html"
            class="block py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600">3rd Semester</a>
          <a href="notes/fourthsem.html"
            class="block py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600">4th Semester</a>
          <a href="notes/fifthsem.html"
            class="block py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600">5th Semester</a>
          <a href="notes/sixthsem.html"
            class="block py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-blue-600">6th Semester</a>
        </div>
      </div>

      <a href="syllabus.html"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium transition-colors">
        <i class="bi bi-stickies-fill text-slate-400"></i> Syllabus
      </a>
      <a href="question.html"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium transition-colors">
        <i class="fa-solid fa-file-circle-question text-slate-400"></i> Question Papers
      </a>
      <a href="library/index.php"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium transition-colors">
        <i class="bi bi-book-half text-slate-400"></i> Digital Library
      </a>
      <a href="feedback.html"
        class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium transition-colors">
        <i class="bi bi-person-rolodex text-slate-400"></i> Feedback
      </a>

      <div
        class="mt-8 px-4 py-4 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-100 dark:border-slate-800/50">
        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Meet the Developers</h4>
        <div class="flex flex-col gap-4">
          <!-- Developer 1 -->
          <div class="flex items-center gap-3">
            <img src="img/rishabh.jpeg" class="w-10 h-10 rounded-full object-cover ring-2 ring-blue-500/20 shadow-sm">
            <div>
              <p class="text-xs font-bold text-slate-900 dark:text-white">Rishabh Kankariya</p>
              <p class="text-[9px] text-blue-600 font-bold tracking-wider uppercase">Lead Developer</p>
            </div>
          </div>
          <!-- Developer 2 -->
          <div class="flex items-center gap-3">
            <img src="img/gourav.jpeg" class="w-10 h-10 rounded-full object-cover ring-2 ring-purple-500/20 shadow-sm">
            <div>
              <p class="text-xs font-bold text-slate-900 dark:text-white">Gourav Sen</p>
              <p class="text-[9px] text-purple-600 font-bold tracking-wider uppercase">UI/UX Developer</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar Footer (Login Options) -->
    <div class="p-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 space-y-3">
      <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2 mb-2">Access Portals</h4>
      <a href="student_login.html"
        class="flex items-center gap-3 w-full px-4 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all">
        <i class="bi bi-mortarboard-fill"></i> Student Login
      </a>
      <div class="grid grid-cols-2 gap-3">
        <a href="admin/admin_login.php"
          class="flex items-center justify-center gap-2 py-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs border border-slate-200 dark:border-slate-700 active:scale-95 transition-all shadow-sm">
          <i class="bi bi-shield-lock-fill text-red-500"></i> Admin
        </a>
        <a href="library_login.html"
          class="flex items-center justify-center gap-2 py-3 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs border border-slate-200 dark:border-slate-700 active:scale-95 transition-all shadow-sm">
          <i class="bi bi-book-fill text-yellow-500"></i> Library
        </a>
      </div>
    </div>
  </div>
  <div id="overlay" onclick="toggleSidebar()"
    class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[990] hidden opacity-0 transition-opacity duration-300">
  </div>

  <!-- 📣 Modern Marquee -->
  <div
    class="bg-gradient-to-r from-slate-900 via-[#1e293b] to-slate-900 text-white relative z-50 overflow-hidden py-2 sm:py-3 border-b border-white/10 shadow-lg">
    <div class="flex items-center h-full">
      <div
        class="bg-white/5 backdrop-blur-md px-3 sm:px-5 py-1 z-20 absolute left-0 h-full flex items-center shadow-[0_0_15px_rgba(0,0,0,0.3)] border-r border-white/10 font-black text-[10px] sm:text-sm tracking-wider uppercase">
        <span class="animate-pulse mr-1 sm:mr-2 drop-shadow-md">🔥</span>
        <span class="hidden sm:inline">Latest Announcement</span>
        <span class="inline sm:hidden">Latest</span>
      </div>
      <div
        class="marquee-content inline-block whitespace-nowrap pl-full animate-marquee-slow hover:pause flex items-center">
        <!-- Content repeated once, slow scroll -->
        <span
          class="px-4 text-[11px] sm:text-sm font-medium tracking-wide flex items-center gap-4 text-slate-100 italic">
          <?php if (!empty($marquee_announcement)): ?>
            <?php echo htmlspecialchars($marquee_announcement); ?> <i class="bi bi-stars text-yellow-400"></i>
          <?php else: ?>
            Welcome to Kitabghar! <i class="bi bi-stars text-yellow-400"></i>
            Your one-stop destination for study materials. <i class="bi bi-stars text-yellow-400"></i>
          <?php endif; ?>
        </span>
      </div>
    </div>
  </div>

  <!-- 🎠 Hero Carousel Section -->
  <div class="relative w-full h-[350px] sm:h-[450px] lg:h-[600px] overflow-hidden bg-slate-900">
    <div class="swiper main-slider w-full h-full">
      <div class="swiper-wrapper">
        <?php foreach ($slides as $slide): ?>
          <div class="swiper-slide relative">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent z-10"></div>
            <img src="admin/img/slides/<?php echo htmlspecialchars($slide['image_url']); ?>" alt="Slide"
              class="w-full h-full object-cover">
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Custom Navigation -->
      <div
        class="swiper-button-prev !text-white !w-12 !h-12 !bg-slate-900/50 !backdrop-blur-md !rounded-full !hidden md:!flex hover:!bg-blue-600 transition-colors border border-white/20 after:!content-[''] justify-center items-center">
        <i class="fa-solid fa-chevron-left text-xl"></i>
      </div>
      <div
        class="swiper-button-next !text-white !w-12 !h-12 !bg-slate-900/50 !backdrop-blur-md !rounded-full !hidden md:!flex hover:!bg-blue-600 transition-colors border border-white/20 after:!content-[''] justify-center items-center">
        <i class="fa-solid fa-chevron-right text-xl"></i>
      </div>

      <!-- Custom Pagination -->
      <div class="swiper-pagination !bottom-8"></div>
    </div>

    <div class="absolute inset-0 z-20 pointer-events-none px-4 md:px-12 flex items-end pb-12 sm:pb-20">
      <div class="max-w-7xl mx-auto w-full">
        <div class="animate-fade-in-up">
          <h2 class="text-white font-black text-2xl sm:text-4xl md:text-5xl lg:text-6xl drop-shadow-2xl mb-2 sm:mb-4">
            Welcome to Kitabghar</h2>
          <p class="text-blue-100 font-bold text-[13px] sm:text-lg md:text-xl drop-shadow-lg max-w-2xl leading-relaxed">
            Your Digital Gateway to Academic Knowledge & Engineering Excellence
          </p>
        </div>
      </div>
    </div>
  </div>
  <!----Carousel---->
  <!-- 📢 Announcements Section -->
  <section class="py-16 bg-slate-50 dark:bg-slate-900 overflow-hidden" data-aos="fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-10">
        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3">
          <span class="p-2 bg-yellow-100 rounded-lg"><i class="bi bi-megaphone-fill text-yellow-600"></i></span>
          Announcements
        </h2>
        <div class="hidden sm:block h-1 flex-grow mx-8 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
      </div>

      <div class="relative group">
        <div id="announcement-cards"
          class="flex overflow-x-auto pb-8 gap-6 snap-x transition-all duration-500 custom-scroll">
          <?php foreach ($announcements as $announcement): ?>
            <div class="flex-shrink-0 w-[300px] sm:w-[350px] snap-center p-1">
              <div
                class="h-full bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div
                  class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-wider uppercase bg-blue-50 text-blue-600 rounded-full">
                  <?php echo htmlspecialchars($announcement['date']); ?>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-3 line-clamp-2">
                  <?php echo htmlspecialchars($announcement['title']); ?>
                </h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-4 ">
                  <?php echo htmlspecialchars($announcement['description']); ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Modern Study Section 1 -->
  <section class="relative py-20 bg-white dark:bg-slate-950 overflow-hidden">
    <div
      class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-50 dark:bg-blue-900/10 rounded-full blur-3xl opacity-50">
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="flex flex-col md:flex-row items-center gap-12">
        <div class="w-full md:w-1/2 space-y-8" data-aos="fade-right">
          <h2 class="text-3xl md:text-5xl font-black text-slate-900 dark:text-white leading-tight">
            Empower Your <span
              class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Learning Journey</span>
            🚀
          </h2>
          <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed max-w-xl">
            Knowledge is power, and every step you take leads to growth! Unlock new opportunities, explore fresh ideas,
            and shape your future. Start learning today and level up your skills with our curated resources.
          </p>
          <div class="flex flex-wrap gap-4">
            <a href="syllabus.html"
              class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg shadow-blue-500/30 transition-all hover:scale-105 active:scale-95">
              Start Now
            </a>
          </div>
        </div>
        <div class="w-full md:w-1/2 flex justify-center" data-aos="fade-left">
          <div class="relative">
            <div class="absolute inset-0 bg-blue-400 rounded-full blur-3xl opacity-20 animate-pulse"></div>
            <dotlottie-player loading="lazy"
              src="https://lottie.host/057ff749-f346-49c1-b8c0-2e6152f2cb87/KBemJVv0nF.lottie" background="transparent"
              speed="1" style="width: 100%; max-width: 450px; height: auto;" loop autoplay>
            </dotlottie-player>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modern Events Section -->
  <section class="py-20 bg-gradient-to-br from-indigo-900 via-blue-900 to-slate-900 text-white overflow-hidden"
    data-aos="fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <span
          class="px-4 py-1.5 bg-white/10 backdrop-blur-md rounded-full text-blue-300 text-sm font-bold uppercase tracking-widest">Campus
          Life</span>
        <h2 class="mt-4 text-4xl md:text-5xl font-black mb-4">Upcoming Events</h2>
        <div class="w-24 h-1 bg-blue-500 mx-auto rounded-full"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($events as $event): ?>
          <div
            class="group relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl overflow-hidden hover:bg-white/10 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/20"
            data-aos="zoom-in">
            <div class="relative h-64 overflow-hidden">
              <img src="img/<?php echo $event['image']; ?>" alt="<?php echo $event['title']; ?>"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
              <div
                class="absolute top-4 right-4 bg-white text-blue-900 px-4 py-2 rounded-2xl font-black text-sm shadow-lg">
                <?php echo htmlspecialchars($event['event_date']); ?>
              </div>
            </div>
            <div class="p-8">
              <h3 class="text-2xl font-bold mb-4 line-clamp-2"><?php echo $event['title']; ?></h3>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 🖼️ Premium Photo Gallery Section (Mosaic Style) -->
  <section class="py-24 bg-white dark:bg-slate-950 relative overflow-hidden" data-aos="fade-up">
    <!-- Subtle Background Glows -->
    <div
      class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3">
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
        <div class="max-w-xl">
          <h2 class="text-5xl md:text-6xl font-black text-slate-900 dark:text-white mb-4 tracking-tighter">
            Visual <span class="text-blue-600">Stories</span>
          </h2>
          <p class="text-slate-500 dark:text-slate-400 font-medium text-lg leading-relaxed">
            A window into the vibrant academic life and cultural milestones at Kitabghar.
          </p>
        </div>
        <button id="openFullGallery"
          class="group flex items-center gap-3 px-8 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-2xl font-bold transition-all hover:ring-4 hover:ring-blue-500/20 active:scale-95 whitespace-nowrap">
          <i class="bi bi-grid-3x3-gap-fill text-blue-600 transition-transform group-hover:rotate-12"></i>
          View Archive
        </button>
      </div>

      <!-- Bento/Mosaic Photo Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 h-auto md:h-[600px]">
        <?php
        $limit = 5;
        $count = 0;
        foreach ($gallery as $image):
          if ($count >= $limit)
            break;
          $count++;

          // Logic for grid shapes (Optimized for balance)
          $class = "";
          if ($count == 1) {
            $class = "md:col-span-2 md:row-span-2 h-[400px] md:h-full"; // Large Featured
          } else {
            $class = "col-span-1 h-[200px] md:h-full"; // Standard
          }
          ?>
          <div
            class="group relative overflow-hidden rounded-[2.5rem] bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 <?php echo $class; ?> cursor-pointer"
            data-aos="zoom-in" data-aos-delay="<?php echo $count * 50; ?>"
            onclick="openPreview(<?php echo $count - 1; ?>)">
            <img loading="lazy" src="img/<?php echo $image['image_path']; ?>" alt="<?php echo $image['title']; ?>"
              class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />

            <!-- Elegant Overlay -->
            <div
              class="absolute inset-x-0 bottom-0 p-8 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-4 group-hover:translate-y-0">
              <h4 class="text-white font-black text-lg sm:text-2xl tracking-tight uppercase">
                <?php echo htmlspecialchars($image['title']); ?>
              </h4>
              <p class="text-blue-400 text-xs font-bold uppercase tracking-widest mt-1">GPC Ujjain</p>
            </div>

            <!-- Glass Interaction Layer -->
            <div
              class="absolute inset-0 border-[8px] border-transparent group-hover:border-white/10 transition-all duration-500 rounded-[2.5rem] pointer-events-none">
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Full Gallery Modal (Professional Revamp) -->
  <div id="fullGalleryModal" class="fixed inset-0 z-[10000] hidden flex items-center justify-center p-4 md:p-10">
    <div class="absolute inset-0 bg-slate-950/95" onclick="closeFullGallery()"></div>

    <div
      class="relative bg-white dark:bg-[#020617] w-full max-w-7xl h-full rounded-[3.5rem] shadow-[0_0_100px_rgba(0,0,0,0.5)] overflow-hidden flex flex-col scale-90 opacity-0 transition-all duration-500 border border-white/5"
      id="fullGalleryContent">

      <!-- Modal Header -->
      <div
        class="px-10 py-8 border-b border-slate-100 dark:border-white/5 flex justify-between items-center bg-white/50 dark:bg-[#020617]/50">
        <div>
          <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tighter uppercase">Image <span
              class="text-blue-600">repository</span></h3>
          <p class="text-sm text-slate-500 font-medium">Browse <?php echo count($gallery); ?> high-definition campus
            visuals</p>
        </div>
        <button onclick="closeFullGallery()"
          class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 hover:text-red-500 hover:rotate-90 transition-all">
          <i class="bi bi-x-lg text-2xl"></i>
        </button>
      </div>

      <!-- Modal Body (Grid List) -->
      <div class="flex-1 overflow-y-auto px-10 py-12 custom-scrollbar">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <?php $repo_idx = 0;
          foreach ($gallery as $image): ?>
            <div
              class="relative aspect-[4/3] rounded-[2rem] overflow-hidden group cursor-pointer border border-slate-200 dark:border-white/5 shadow-sm hover:shadow-2xl hover:ring-4 hover:ring-blue-500/30 transition-all"
              onclick="openPreview(<?php echo $repo_idx; ?>)">
              <img loading="lazy" src="img/<?php echo $image['image_path']; ?>"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
              <div
                class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <div class="flex flex-col items-center gap-2">
                  <div
                    class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-slate-900 shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-transform">
                    <i class="bi bi-arrows-fullscreen"></i>
                  </div>
                  <span
                    class="text-white font-black text-[10px] uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all delay-100">Quick
                    Preview</span>
                </div>
              </div>
            </div>
            <?php $repo_idx++; endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- 🖼️ Image Detail Viewer Modal -->
  <div id="imageDetailModal" class="fixed inset-0 z-[10001] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-50" onclick="closePreview()"></div>

    <div
      class="relative w-full max-w-5xl mx-auto scale-95 opacity-0 transition-all duration-500 flex flex-col items-center justify-center min-h-[80vh]"
      id="previewContent">

      <!-- Image Card Wrapper -->
      <div
        class="relative bg-white rounded-[3rem] p-4 sm:p-6 shadow-[0_30px_100px_rgba(0,0,0,0.15)] border border-slate-100 max-w-full w-fit mx-auto overflow-hidden group">

        <!-- Close Button (Premium Desktop Position) -->
        <button onclick="closePreview()"
          class="hidden md:flex absolute top-6 right-6 w-10 h-10 rounded-full bg-slate-100/80 hover:bg-rose-500 hover:text-white text-slate-500 items-center justify-center transition-all z-[10003] shadow-sm active:scale-90">
          <i class="bi bi-x-lg"></i>
        </button>

        <div
          class="relative bg-slate-50 rounded-[2rem] overflow-hidden flex items-center justify-center border border-slate-100">
          <img src="" id="previewImg"
            class="max-w-full h-auto max-h-[60vh] md:max-h-[70vh] object-contain transition-all duration-500 block">

          <!-- Floating Navigation Buttons (Desktop Internal) -->
          <button onclick="prevImage(event)"
            class="absolute left-4 w-12 h-12 rounded-full bg-white/80 hover:bg-white text-slate-900 items-center justify-center transition-all shadow-xl hidden md:flex opacity-0 group-hover:opacity-100">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button onclick="nextImage(event)"
            class="absolute right-4 w-12 h-12 rounded-full bg-white/80 hover:bg-white text-slate-900 items-center justify-center transition-all shadow-xl hidden md:flex opacity-0 group-hover:opacity-100">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>

        <div class="mt-6 text-center px-4">
          <h4 id="previewTitle"
            class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tighter line-clamp-1">Image Title
          </h4>
          <p class="text-blue-600 font-bold text-[9px] sm:text-[10px] uppercase tracking-[0.3em] mt-2 opacity-60">
            Official Repository</p>
        </div>
      </div>

      <!-- Desktop Floating Nav (Revised) -->
      <button onclick="prevImage(event)"
        class="fixed left-8 top-1/2 -translate-y-1/2 w-16 h-16 rounded-full bg-white/10 hover:bg-white/40 text-slate-900 items-center justify-center transition-all shadow-2xl hidden md:flex border border-white/20">
        <i class="bi bi-chevron-left text-2xl"></i>
      </button>
      <button onclick="nextImage(event)"
        class="fixed right-8 top-1/2 -translate-y-1/2 w-16 h-16 rounded-full bg-white/10 hover:bg-white/40 text-slate-900 items-center justify-center transition-all shadow-2xl hidden md:flex border border-white/20">
        <i class="bi bi-chevron-right text-2xl"></i>
      </button>

      <!-- Mobile Navigation -->
      <div class="flex justify-center gap-4 mt-8 md:hidden">
        <button onclick="prevImage(event)"
          class="w-16 h-16 rounded-3xl bg-white text-slate-900 flex items-center justify-center shadow-xl active:scale-95 transition-transform border border-slate-100 text-xl"><i
            class="bi bi-chevron-left"></i></button>
        <button onclick="nextImage(event)"
          class="w-16 h-16 rounded-3xl bg-white text-slate-900 flex items-center justify-center shadow-xl active:scale-95 transition-transform border border-slate-100 text-xl"><i
            class="bi bi-chevron-right"></i></button>
        <button onclick="closePreview()"
          class="w-16 h-16 rounded-3xl bg-rose-500 text-white flex items-center justify-center shadow-xl active:scale-95 transition-transform text-xl"><i
            class="bi bi-x-lg"></i></button>
      </div>
    </div>
  </div>

  <!-- 📽️ Modern Video Gallery Section -->
  <section class="py-20 bg-slate-50 dark:bg-slate-900" data-aos="fade-up">
    <div class="max-w-7xl mx-auto px-4">
      <h2
        class="text-4xl md:text-5xl font-black text-center text-slate-900 dark:text-white mb-16 flex items-center justify-center gap-4 uppercase tracking-tighter">
        <i class="bi bi-camera-reels-fill text-orange-500"></i>
        Video Gallery
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ($videos as $video): ?>
          <div
            class="group relative bg-[#0a1128] border border-white/5 rounded-[2.5rem] overflow-hidden transition-all duration-700 hover:shadow-[0_20px_50px_rgba(249,115,22,0.15)] hover:-translate-y-2"
            data-aos="fade-up">
            <!-- Video Container with Portrait Aspect -->
            <div class="relative aspect-[9/16] sm:aspect-[3/4] overflow-hidden">
              <video class="w-full h-full object-cover" loop muted playsinline>
                <source src="img/<?php echo $video['video_path']; ?>" type="video/mp4">
              </video>

              <!-- Play Overlay -->
              <div
                class="absolute inset-0 bg-black/40 opacity-100 group-hover:bg-black/20 transition-all duration-500 flex items-center justify-center pointer-events-none">
                <div
                  class="w-16 h-16 rounded-full bg-orange-500/90 text-white flex items-center justify-center shadow-[0_0_30px_rgba(249,115,22,0.5)] transform transition-all duration-500 group-hover:scale-110">
                  <i class="bi bi-play-fill text-3xl ml-1"></i>
                </div>
              </div>

              <!-- Content Overlay (Bottom) -->
              <div
                class="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-black/90 via-black/40 to-transparent pt-12">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-lg">
                    <i class="bi bi-play-btn-fill text-xl"></i>
                  </div>
                  <div>
                    <h4 class="font-black text-white text-lg tracking-tight line-clamp-1 uppercase">
                      <?php echo htmlspecialchars($video['title']); ?>
                    </h4>
                    <p class="text-[10px] text-orange-400 font-bold tracking-[0.2em] uppercase">Campus Live</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Interaction Trigger -->
            <button class="absolute inset-0 z-10 w-full h-full opacity-0 video-trigger"
              data-video="img/<?php echo $video['video_path']; ?>"
              data-title="<?php echo htmlspecialchars($video['title']); ?>"></button>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 📝 Modern Contribution Section -->
  <section id="contribution-section-parallax" class="relative py-32 overflow-hidden bg-slate-900">
    <div class="absolute inset-0 z-0 opacity-40">
      <img src="img/section.webp" alt="Background" class="w-full h-full object-cover">
    </div>
    <div class="absolute inset-0 bg-gradient-to-b from-blue-900/80 to-slate-900/90 z-0"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-16">
        <h2 class="text-4xl md:text-6xl font-black text-white mb-6 flex items-center justify-center gap-4">
          <i class="bi bi-journal-arrow-up text-blue-400"></i>
          <span>Contribute Notes</span>
        </h2>
        <p class="text-xl text-blue-100/80 font-medium">Help your juniors and earn some good karma ✨</p>
      </div>

      <div
        class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl p-8 sm:p-12 border border-white/10 max-w-2xl mx-auto"
        data-aos="zoom-in">
        <form action="php/contribute_notes.php" method="POST" enctype="multipart/form-data" class="space-y-6"
          onsubmit="showToast(); return true;">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Student Name</label>
              <input type="text" name="student_name" required
                class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 px-5 py-4 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Notes Title</label>
              <input type="text" name="note_title" required
                class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 px-5 py-4 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all">
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Semester</label>
            <select name="semester" required
              class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 px-5 py-4 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
              <option value="" class="text-slate-500">Select Semester</option>
              <option value="1">1st Semester</option>
              <option value="2">2nd Semester</option>
              <option value="3">3rd Semester</option>
              <option value="4">4th Semester</option>
              <option value="5">5th Semester</option>
              <option value="6">6th Semester</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Upload Your Notes
              (PDF/Doc)</label>
            <div class="relative group cursor-pointer">
              <input type="file" name="note_file" id="note_file_input" accept=".pdf,.doc,.docx" required
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                onchange="if(this.files.length > 0) document.getElementById('file_upload_text').textContent = this.files[0].name;">
              <div
                class="flex items-center justify-center gap-3 w-full border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-8 bg-slate-50 dark:bg-slate-900 group-hover:border-blue-500 transition-colors">
                <i class="bi bi-cloud-upload text-3xl text-slate-400 group-hover:text-blue-500 transition-colors"></i>
                <span id="file_upload_text"
                  class="text-slate-500 font-medium group-hover:text-slate-700 dark:group-hover:text-slate-200">Click or
                  drag files to upload</span>
              </div>
            </div>
          </div>

          <button type="submit" id="contributeSubmitBtn"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-500/20 flex items-center justify-center gap-3 transition-all hover:scale-[1.02] active:scale-95">
            <span id="contributeBtnText"><i class="bi bi-send-fill mr-2"></i>Submit Notes</span>
            <div id="contributeBtnSpinner"
              class="hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          </button>
        </form>
      </div>
    </div>
  </section>
  <!-- 🎓 Principal's Message Section -->
  <section class="py-24 bg-[#050b18] relative overflow-hidden">
    <!-- Background Glows -->
    <div
      class="absolute top-0 left-0 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px] -translate-x-1/2 -translate-y-1/2">
    </div>
    <div
      class="absolute bottom-0 right-0 w-96 h-96 bg-orange-600/10 rounded-full blur-[120px] translate-x-1/2 translate-y-1/2">
    </div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
      <div class="grid lg:grid-cols-2 gap-16 items-center">

        <!-- Image Container -->
        <div class="relative" data-aos="fade-right">
          <div
            class="relative z-10 rounded-[3rem] overflow-hidden border-8 border-white/5 shadow-2xl transform  hover:rotate-0 transition-transform duration-700 bg-slate-800">
            <img src="img/Dr. R. C. Gupta.jpg" alt="Principal"
              class="w-full h-[400px] md:h-[500px] object-cover hover:scale-105 transition-transform duration-700">
          </div>
          <!-- Decorative Elements -->
          <div
            class="absolute -bottom-6 -right-6 w-32 h-32 bg-orange-500 rounded-3xl -z-10 rotate-12 opacity-20 blur-2xl">
          </div>
          <div class="absolute -top-6 -left-6 w-32 h-32 bg-blue-500 rounded-full -z-10 opacity-20 blur-2xl"></div>

          <!-- Status Badge -->
          <div
            class="absolute -bottom-4 left-1/2 -translate-x-1/2 bg-white/10 backdrop-blur-2xl border border-white/20 px-8 py-4 rounded-2xl shadow-2xl z-20 whitespace-nowrap"
            data-aos="zoom-in" data-aos-delay="400">
            <p class="text-[10px] text-orange-400 font-bold uppercase tracking-[0.3em] mb-1">Inspiration & Leadership
            </p>
            <h4 class="text-white font-black text-xl uppercase tracking-tighter">Leading GPC Ujjain</h4>
          </div>
        </div>

        <!-- Content Area -->
        <div class="space-y-8" data-aos="fade-left">
          <div class="inline-block px-4 py-2 bg-blue-500/10 border border-blue-500/20 rounded-full">
            <span class="text-blue-400 font-bold text-xs uppercase tracking-[0.3em]">From the Principal's Desk</span>
          </div>

          <h2 class="text-4xl md:text-5xl font-black text-white leading-tight">
            Empowering <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">Skilled
              Technocrats</span> for a Brighter Future
          </h2>

          <div class="relative">
            <blockquote class="text-xl text-slate-200 leading-relaxed font-black italic tracking-tight font-serif pt-4">
              " Technical education equips students with the practical skills and knowledge needed to work in industries
              that require specialized skills."
            </blockquote>
          </div>

          <div class="space-y-4 text-slate-400 leading-relaxed text-[15px] sm:text-base">
            <p>
              Dear students, as the Principal of this technical college, I want to take this opportunity to remind you
              of the importance of technical education and how it can benefit your career prospects. Technical education
              plays a vital role in the economic development of a country, bridging the skills gap between theoretical
              learning and workforce requirements.
            </p>
            <p>
              As technology continues to advance, the demand for skilled Technocrats is increasing. Technical education
              provides you with the necessary skills to pursue high-paying careers and helps develop personal qualities
              such as self-confidence, independence, and responsibility.
            </p>
            <p>
              Finally, I want to assure you that we are committed to providing you with the best technical education and
              support. Our faculty and staff are here to help you achieve your goals and fulfill your potential. I wish
              you all the best in your studies and future endeavors.
            </p>
          </div>

          <div class="pt-10 flex items-center gap-6">
            <div class="h-px bg-white/10 flex-grow"></div>
            <div class="text-right">
              <h4 class="text-2xl font-black text-white uppercase tracking-tighter">Dr. R. C. Gupta</h4>
              <p class="text-orange-500 font-bold text-xs uppercase tracking-widest mt-1">Principal • Government
                Polytechnic College Ujjain</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
  <!-- 👨‍🏫 Modern Faculty Section -->
  <section class="py-24 bg-white dark:bg-slate-950 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row items-end justify-between mb-16 gap-6">
        <div class="space-y-4">
          <span class="text-blue-600 font-extrabold uppercase tracking-widest text-sm text-blue-500">Our Mentors</span>
          <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white">Meet Our <span
              class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">CSE Faculty</span></h2>
        </div>
        <p class="max-w-md text-slate-600 dark:text-slate-400 font-medium">Experienced educators dedicated to shaping
          the next generation of computer science professionals.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php
        $faculties = [
          ['name' => 'Shri M. Munshi', 'role' => 'HEAD OF DEPARTMENT', 'bio' => 'Leading advancements in computer science education', 'img' => 'img/hod-computer.jpg', 'email' => 'm.munshi@nic.in', 'phone' => '+918319971084'],
          ['name' => 'Shri R. Kumar', 'role' => 'SELECTION GRADE LECTURER', 'bio' => 'Specialist in C, C++, and Java education', 'img' => 'img/Shri R Kumar.jpg', 'email' => 'kumarpolyujn@gmail.com', 'phone' => '+919009873151'],
          ['name' => 'Shri Thomas Meda', 'role' => 'SENIOR GRADE LECTURER', 'bio' => 'Passionate educator in computing systems', 'img' => 'img/Thomas_sir.jpg', 'email' => 'tom.upcu@gmail.com', 'phone' => '+919407126618'],
          ['name' => 'Shri. Ramlal Agrawal', 'role' => 'FACULTY LECTURER', 'bio' => 'Dedicated to software development education', 'img' => 'img/Ram_Sir.jpg', 'email' => 'ramsirg.edu@gmail.com', 'phone' => '+918224015098'],
          ['name' => 'Smt. Rashi Saxena', 'role' => 'SENIOR GRADE LECTURER', 'bio' => 'Passionate about databases and data structures', 'img' => 'img/dummy.png', 'email' => 'rashi8808@gmail.com', 'phone' => '+917000174175'],
          ['name' => 'Smt. Megha Malviya', 'role' => 'FACULTY LECTURER', 'bio' => 'Focus on problem-solving for engineers', 'img' => 'img/faculty-2.jpg', 'email' => 'meghamalviya92@gmail.com', 'phone' => '+917828068048'],
          ['name' => 'Shri. Ishak Ansari', 'role' => 'FACULTY CSE', 'bio' => 'Committed to fostering software excellence', 'img' => 'img/Ansari_Sir.jpg', 'email' => 'ansari@gmail.com', 'phone' => '+917697164221'],
          ['name' => 'Shri. Dharmendra', 'role' => 'LAB TECHNICIAN', 'bio' => 'Expertise in computer maintenance and support', 'img' => 'img/Dharmendra_Sir.jpg', 'email' => 'dharmend@gmail.com', 'phone' => '+911234567890'],
        ];
        foreach ($faculties as $f):
          ?>
          <div
            class="group bg-slate-50 dark:bg-slate-900 rounded-[2rem] p-6 hover:bg-white dark:hover:bg-slate-800 border border-transparent hover:border-slate-100 dark:hover:border-slate-700 transition-all duration-500 hover:shadow-2xl hover:shadow-blue-500/10"
            data-aos="fade-up">
            <div class="relative mb-6">
              <div class="aspect-square rounded-2xl overflow-hidden shadow-inner">
                <img loading="lazy" src="<?php echo $f['img']; ?>" alt="<?php echo $f['name']; ?>"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
              </div>
              <div
                class="absolute -bottom-4 left-1/2 -translate-x-1/2 flex gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-y-2 group-hover:translate-y-0">
                <a href="mailto:<?php echo $f['email']; ?>"
                  class="p-3 bg-white dark:bg-slate-800 text-blue-600 rounded-xl shadow-lg hover:bg-blue-600 hover:text-white transition-all"><i
                    class="bi bi-envelope-fill"></i></a>
                <a href="tel:<?php echo $f['phone']; ?>"
                  class="p-3 bg-white dark:bg-slate-800 text-green-600 rounded-xl shadow-lg hover:bg-green-600 hover:text-white transition-all"><i
                    class="bi bi-telephone-fill"></i></a>
              </div>
            </div>
            <div class="text-center px-2">
              <h3 class="text-xl font-black text-slate-800 dark:text-white mb-1 uppercase tracking-tight line-clamp-1">
                <?php echo $f['name']; ?>
              </h3>
              <p class="text-blue-600 font-bold text-xs uppercase mb-3 line-clamp-1"><?php echo $f['role']; ?></p>
              <p class="text-slate-500 dark:text-slate-400 text-sm italic line-clamp-2"><?php echo $f['bio']; ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- 🤖 Modern Chatbot Widget (Side Docked) -->
  <div class="fixed bottom-24 left-0 z-[9990] flex flex-col items-start gap-4 pl-1" id="chatbot-wrapper">
    <!-- Main Button -->
    <button id="chatbot-btn"
      class="w-12 h-14 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-r-3xl rounded-l-none shadow-[0_8px_30px_rgb(37,99,235,0.4)] flex items-center justify-center text-white text-2xl transition-all hover:w-16 hover:brightness-110 active:scale-95 group border-y border-r border-white/20 opacity-40 hover:opacity-100 backdrop-blur-sm">
      <i class="fa-solid fa-robot group-hover:rotate-12 transition-transform"></i>
    </button>
  </div>

  <!-- 🤖 Chat Window -->
  <div id="chatbot-container"
    class="fixed bottom-24 left-4 right-4 sm:left-16 sm:right-auto z-[9999] w-auto sm:w-[420px] h-[550px] sm:h-[650px] max-h-[85vh] bg-gradient-to-br from-[#050b18] via-[#0a1128] to-[#050b18] rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.8)] border border-white/10 hidden flex-col overflow-hidden animate-fade-in-up origin-bottom-left transition-all duration-500">

    <!-- Header -->
    <div class="px-6 py-5 flex items-center justify-between border-b border-white/5 bg-white/5 backdrop-blur-md">
      <div class="flex items-center gap-4">
        <div class="relative group">
          <div
            class="w-14 h-14 bg-gradient-to-tr from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg transform group-hover:scale-105 transition-transform duration-300">
            <span class="text-3xl">🤖</span>
          </div>
          <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-slate-900 rounded-full">
            <div class="w-full h-full bg-green-400 rounded-full animate-ping opacity-75"></div>
          </div>
        </div>
        <div>
          <h3 class="font-extrabold text-white text-xl tracking-tight leading-none">E-Know AI</h3>
          <span class="text-[10px] font-bold text-blue-400 uppercase tracking-[0.2em] mt-2 block">Academic
            Assistant</span>
        </div>
      </div>
      <button id="close-chatbot"
        class="w-10 h-10 flex items-center justify-center text-white/40 hover:text-white hover:bg-white/10 rounded-full transition-all active:scale-90 border border-white/5">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <!-- Chat Area -->
    <div id="chatbox" class="flex-1 overflow-y-auto p-6 space-y-6 bg-transparent chatbot-scroll relative scroll-smooth">
      <!-- Welcome Message -->
      <div class="flex items-start gap-4 bot-message group">
        <div
          class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl shadow-sm shrink-0 border border-white/10 group-hover:bg-white/20 transition-colors">
          🤖</div>
        <div
          class="bg-white/10 backdrop-blur-md p-4 rounded-3xl rounded-tl-none border border-white/10 text-slate-100 text-base leading-relaxed shadow-xl">
          Hello! I'm <span class="text-blue-400 font-bold">E-Know</span>. How can I assist you with your studies today?
        </div>
      </div>

      <!-- Typing Indicator -->
      <div id="typing-indicator" class="hidden items-start gap-4 bot-message animate-fade-in">
        <div
          class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl shrink-0 border border-white/10">
          🤖</div>
        <div class="bg-white/5 backdrop-blur-md p-4 px-6 rounded-3xl rounded-tl-none border border-white/10 flex gap-2">
          <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
          <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce [animation-delay:0.2s]"></div>
          <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce [animation-delay:0.4s]"></div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Menu -->
    <div id="quick-menu"
      class="hidden absolute bottom-[6.5rem] left-6 right-6 bg-slate-800/90 backdrop-blur-3xl border border-white/10 p-5 rounded-[2rem] shadow-2xl z-50 transform transition-all duration-500 origin-bottom scale-95 opacity-0">
      <div class="flex items-center justify-between mb-4 px-2">
        <h4 class="text-[11px] font-black text-blue-400 uppercase tracking-[0.2em]">Quick Services</h4>
        <button id="close-quick-menu"
          class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center text-white/40 hover:text-white transition-colors"><i
            class="fa-solid fa-xmark text-xs"></i></button>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <button onclick="sendCommand('exam form')"
          class="p-3 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl text-left transition-all flex items-center gap-3 group active:scale-95">
          <div
            class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-all">
            <i class="fa-solid fa-file-pen text-sm"></i>
          </div>
          <span class="text-[13px] font-bold text-slate-200">Exam Form</span>
        </button>
        <button onclick="sendCommand('syllabus')"
          class="p-3 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl text-left transition-all flex items-center gap-3 group active:scale-95">
          <div
            class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center group-hover:bg-purple-500 group-hover:text-white transition-all">
            <i class="fa-solid fa-book-open text-sm"></i>
          </div>
          <span class="text-[13px] font-bold text-slate-200">Syllabus</span>
        </button>
        <button onclick="sendCommand('faculty')"
          class="p-3 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl text-left transition-all flex items-center gap-3 group active:scale-95">
          <div
            class="w-9 h-9 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition-all">
            <i class="fa-solid fa-user-tie text-sm"></i>
          </div>
          <span class="text-[13px] font-bold text-slate-200">Faculty</span>
        </button>
        <button onclick="sendCommand('contact')"
          class="p-3 bg-white/5 hover:bg-white/10 border border-white/5 rounded-2xl text-left transition-all flex items-center gap-3 group active:scale-95">
          <div
            class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition-all">
            <i class="fa-solid fa-headset text-sm"></i>
          </div>
          <span class="text-[13px] font-bold text-slate-200">Support</span>
        </button>
      </div>
    </div>

    <!-- Input Area -->
    <div class="p-6 bg-transparent relative z-[60]">
      <div
        class="flex items-center gap-3 bg-white/5 backdrop-blur-xl p-2 rounded-[2rem] border border-white/10 focus-within:border-blue-500/50 focus-within:bg-white/10 transition-all shadow-2xl">
        <button id="toggle-quick-menu"
          class="w-12 h-12 rounded-2xl bg-white/5 text-white/60 hover:text-blue-400 hover:bg-white/10 transition-all flex items-center justify-center active:scale-90 border border-white/5">
          <i class="fa-solid fa-bars-staggered text-lg"></i>
        </button>
        <input type="text" id="user-input" placeholder="Type a message..."
          class="flex-1 bg-transparent border-none outline-none text-[15px] text-white px-2 font-medium placeholder:text-white/20"
          autocomplete="off">
        <button id="send-btn"
          class="w-12 h-12 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl flex items-center justify-center shadow-[0_0_20px_rgba(37,99,235,0.3)] transition-all hover:scale-105 active:scale-90 group">
          <i class="fa-solid fa-paper-plane text-lg group-hover:rotate-12 transition-transform"></i>
        </button>
      </div>
      <div class="flex justify-between mt-4 px-2 items-center">
        <button id="clear-btn"
          class="text-[10px] font-black text-white/20 hover:text-red-500/80 transition-all flex items-center gap-2 uppercase tracking-widest"><i
            class="fa-solid fa-trash-can"></i> Clear History</button>
        <div
          class="flex items-center gap-1.5 grayscale opacity-30 hover:grayscale-0 hover:opacity-100 transition-all cursor-default">
          <span class="text-[9px] font-bold text-white uppercase tracking-tighter">Powered by</span>
          <span class="text-[10px] font-black text-blue-500 leading-none">Kitabghar</span>
        </div>
      </div>
    </div>
  </div>
  </div>
  <!-- 📞 Modern Contact Support Section -->
  <section class="py-24 bg-white dark:bg-slate-950">
    <div class="max-w-7xl mx-auto px-4 flex flex-col lg:flex-row items-center gap-16" data-aos="fade-up">
      <div class="w-full lg:w-1/2 flex justify-center order-2 lg:order-1">
        <div class="relative w-full max-w-sm">
          <div class="absolute inset-0 bg-orange-400 rounded-full blur-3xl opacity-20 animate-pulse"></div>
          <dotlottie-player loading="lazy"
            src="https://lottie.host/983a5a56-ca6f-4118-83f9-90e4bf7edd84/sEvRQCZn2B.lottie" background="transparent"
            speed="1" style="width: 100%; height: auto;" loop autoplay>
          </dotlottie-player>
        </div>
      </div>

      <div class="w-full lg:w-1/2 order-1 lg:order-2 space-y-10">
        <div>
          <h2 class="text-4xl md:text-5xl font-black text-slate-900 dark:text-white mb-4">Contact Support</h2>
          <p class="text-lg text-slate-600 dark:text-slate-400 font-medium">Have questions? We're here to help.</p>
        </div>
        <form action="php/contact.php" method="POST" class="space-y-6">
          <!-- Honeypot -->
          <input type="text" name="website" style="display:none !important" tabindex="-1" autocomplete="off">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Full Name</label>
              <input type="text" name="name" required placeholder="John Doe"
                class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 px-5 py-4 focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
            </div>
            <div class="space-y-2">
              <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Email Address</label>
              <input type="email" name="email" required placeholder="john@example.com"
                class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 px-5 py-4 focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
            </div>
          </div>
          <div class="space-y-2">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Subject</label>
            <input type="text" name="subject" required placeholder="How can we help?"
              class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 px-5 py-4 focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all">
          </div>
          <div class="space-y-2">
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Your Message</label>
            <textarea name="message" rows="4" required placeholder="Tell us more about your inquiry..."
              class="w-full rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 px-5 py-4 focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all"></textarea>
          </div>
          <button type="submit" id="contactSubmitBtn"
            class="w-full bg-orange-600 hover:bg-orange-700 text-white font-black py-5 rounded-2xl shadow-xl shadow-orange-500/20 transition-all hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
            <span id="contactBtnText">Send Message</span>
            <div id="contactBtnSpinner"
              class="hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
          </button>
        </form>
      </div>
    </div>
  </section>

  <!-- 🔔 Toast Container -->
  <!-- 🔔 Toast Container -->
  <div id="toast-container" class="fixed top-6 right-6 z-[9999] space-y-4 max-w-sm"></div>

  <!-- 🔔 Special Contribution Toast (Hidden) -->
  <div id="toastContributeNotes"
    class="fixed top-24 right-6 bg-emerald-600 text-white px-6 py-4 rounded-xl shadow-2xl hidden z-[10000] animate-fade-in-up flex items-center gap-3">
    <i class="bi bi-check-circle-fill text-2xl text-emerald-200"></i>
    <div>
      <h4 class="font-bold">Thank You!</h4>
      <p class="text-sm">Your notes have been submitted.</p>
    </div>
  </div>


  <!-- 🚀 Quick Links Section -->
  <section class="py-16 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-4">Quick Links</h2>
        <div class="w-16 h-1 bg-blue-500 mx-auto rounded-full"></div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6" data-aos="fade-up">

        <!-- RGPV Diploma -->
        <a href="https://www.rgpvdiploma.in/Index.aspx" target="_blank"
          class="group bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all border border-slate-100 dark:border-slate-700 flex flex-col items-center gap-4 text-center hover:-translate-y-1">
          <div
            class="w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center p-2 group-hover:scale-110 transition-transform">
            <img src="img/rgpv diploma.png" style="border-radius:50%;"
              class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
          </div>
          <div>
            <h3 class="font-bold text-slate-800 dark:text-white text-sm">RGPV Diploma</h3>
            <span class="text-xs text-blue-600 font-semibold mt-1 inline-block">Visit Site &rarr;</span>
          </div>
        </a>

        <!-- E-Kumbh -->
        <a href="https://ekumbh.aicte-india.org/" target="_blank"
          class="group bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all border border-slate-100 dark:border-slate-700 flex flex-col items-center gap-4 text-center hover:-translate-y-1">
          <div
            class="w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center p-2 group-hover:scale-110 transition-transform">
            <img src="img/ekumbh.jfif" style="border-radius:50%;"
              class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
          </div>
          <div>
            <h3 class="font-bold text-slate-800 dark:text-white text-sm">E-Kumbh</h3>
            <span class="text-xs text-blue-600 font-semibold mt-1 inline-block">Visit Site &rarr;</span>
          </div>
        </a>

        <!-- MP TAAS -->
        <a href="https://www.tribal.mp.gov.in/MPTAAS" target="_blank"
          class="group bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all border border-slate-100 dark:border-slate-700 flex flex-col items-center gap-4 text-center hover:-translate-y-1">
          <div
            class="w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center p-2 group-hover:scale-110 transition-transform">
            <img src="img/mptaas.jfif" style="border-radius:50%;"
              class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
          </div>
          <div>
            <h3 class="font-bold text-slate-800 dark:text-white text-sm">MP TAAS</h3>
            <span class="text-xs text-blue-600 font-semibold mt-1 inline-block">Visit Site &rarr;</span>
          </div>
        </a>

        <!-- MP DTE -->
        <a href="https://dte.mponline.gov.in/portal/services/onlinecounselling/counshomepage/home.aspx" target="_blank"
          class="group bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all border border-slate-100 dark:border-slate-700 flex flex-col items-center gap-4 text-center hover:-translate-y-1">
          <div
            class="w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center p-2 group-hover:scale-110 transition-transform">
            <img src="img/mpdte.jfif" style="border-radius:50%;"
              class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
          </div>
          <div>
            <h3 class="font-bold text-slate-800 dark:text-white text-sm">MP DTE</h3>
            <span class="text-xs text-blue-600 font-semibold mt-1 inline-block">Visit Site &rarr;</span>
          </div>
        </a>

        <!-- MP Online Portal -->
        <a href="https://mponline.gov.in/portal/" target="_blank"
          class="group bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all border border-slate-100 dark:border-slate-700 flex flex-col items-center gap-4 text-center hover:-translate-y-1">
          <div
            class="w-20 h-20 rounded-full bg-white dark:bg-slate-700 flex items-center justify-center p-2 group-hover:scale-110 transition-transform shadow-inner">
            <img src="img/mponline_logo.png" style="border-radius:50%;" class="w-full h-full object-contain">
          </div>
          <div>
            <h3 class="font-bold text-slate-800 dark:text-white text-sm">MP Online</h3>
            <span class="text-xs text-blue-600 font-semibold mt-1 inline-block">Visit Site &rarr;</span>
          </div>
        </a>

        <!-- GPC Ujjain -->
        <a href="https://gpcujjain.ac.in/" target="_blank"
          class="group bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all border border-slate-100 dark:border-slate-700 flex flex-col items-center gap-4 text-center hover:-translate-y-1">
          <div
            class="w-20 h-20 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center p-2 group-hover:scale-110 transition-transform">
            <img src="img/gpcu.png" style="border-radius:50%;"
              class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal">
          </div>
          <div>
            <h3 class="font-bold text-slate-800 dark:text-white text-sm">GPC Ujjain</h3>
            <span class="text-xs text-blue-600 font-semibold mt-1 inline-block">Visit Site &rarr;</span>
          </div>
        </a>

      </div>
    </div>
  </section>

  <!-- 📍 Map Section -->
  <div class="h-96 w-full relative z-10 grayscale hover:grayscale-0 transition-all duration-700">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3668.31251439391!2d75.80250387470481!3d23.158791411232208!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39637441f779fedd%3A0x33a7363bd987f1c4!2sGovernment%20Polytechnic%20College%20ujjain!5e0!3m2!1sen!2sin!4v1731958773597!5m2!1sen!2sin"
      class="w-full h-full border-0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
    </iframe>
    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950 to-transparent pointer-events-none">
    </div>
  </div>

  <!-- 🦶 Modern Footer -->
  <footer class="footer bg-slate-950 text-slate-300 pt-20 pb-10 relative overflow-hidden font-sans">

    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5"
      style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 32px 32px;"></div>

    <div class="max-w-7xl mx-auto px-4 relative z-10">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

        <!-- Brand Column -->
        <div class="space-y-6">
          <div class="flex items-center gap-3">
            <img src="img/kitabghar.png" alt="Kitabghar Logo" class="h-14 rounded-xl shadow-2xl border border-white/10">
          </div>
          <p class="text-slate-400 text-sm leading-relaxed">
            Empowering Computer Science students with accessible, high-quality learning resources. Join our community of
            learners today.
          </p>
          <div class="flex gap-4">
            <a href="https://www.instagram.com/ekitabghar/"
              class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-gradient-to-r hover:from-[#833ab4] hover:via-[#fd1d1d] hover:to-[#fcb045] hover:bg-clip-text hover:text-transparent transition-colors">
              <i class="fa-brands fa-instagram text-lg"></i>
            </a>
            <a href="#"
              class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors"><i
                class="fa-brands fa-facebook-f"></i></a>
            <a href="#"
              class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors"><i
                class="fa-brands fa-youtube"></i></a>
          </div>
        </div>

        <!-- Links Column -->
        <div>
          <h4 class="text-white font-bold text-lg mb-6 flex items-center gap-2"><span
              class="w-2 h-2 bg-blue-500 rounded-full"></span> Useful Links</h4>
          <ul class="space-y-3 text-sm">
            <li><a href="index.php" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> Home</a></li>
            <li><a href="about.html" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> About Us</a></li>
            <li><a href="syllabus.html" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> Syllabus</a></li>
            <li><a href="question.html" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> Question Papers</a></li>
            <li><a href="features.html" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> Features</a></li>
          </ul>
        </div>

        <!-- Legal Column -->
        <div>
          <h4 class="text-white font-bold text-lg mb-6 flex items-center gap-2"><span
              class="w-2 h-2 bg-green-500 rounded-full"></span> Legal</h4>
          <ul class="space-y-3 text-sm">
            <li><a href="term&condition.html" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> Terms & Conditions</a></li>
            <li><a href="privacy&policy.html" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> Privacy Policy</a></li>
            <li><a href="help&faq.html" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> Help & FAQ</a></li>
            <li><a href="feedback.html" class="hover:text-blue-400 transition-colors flex items-center gap-2"><i
                  class="fa-solid fa-chevron-right text-xs text-slate-600"></i> Feedback</a></li>
          </ul>
        </div>

        <!-- Contact Column -->
        <div>
          <h4 class="text-white font-bold text-lg mb-6 flex items-center gap-2"><span
              class="w-2 h-2 bg-orange-500 rounded-full"></span> Contact</h4>
          <ul class="space-y-4 text-sm">
            <li class="flex items-start gap-3">
              <i class="fa-solid fa-location-dot mt-1 text-blue-500"></i>
              <span>Govt Polytechnic College,<br>Dewas Road, Ujjain - 456001 (M.P)</span>
            </li>
            <li class="flex items-center gap-3">
              <i class="fa-solid fa-phone text-blue-500"></i>
              <span>+91 7697164221</span>
            </li>
            <li class="flex items-center gap-3">
              <i class="fa-solid fa-envelope text-blue-500"></i>
              <span>ekitabghar[at]gmail[dot]com</span>
            </li>
          </ul>
        </div>

      </div>

      <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <p class="text-sm text-slate-500">
          © <?php echo date('Y'); ?> Kitabghar | All Rights Reserved | This website is designed, developed and
          maintained
          <span class="text-blue-400 font-medium">
            <a href="https://www.linkedin.com/in/rishabh-kankariya-202a93252/" target="_blank"
              class="hover:underline">Rishabh Kankariya</a> ,
            <a href="https://www.linkedin.com/in/gourav-sen-614b62375/" target="_blank" class="hover:underline">Gourav
              Sen</a>
          </span>.
        </p>

        <!-- Visitor Counter -->
        <div
          class="visitor-container flex items-center gap-3 bg-slate-900 border border-slate-800 rounded-lg px-4 py-2">
          <span class="text-xs font-bold text-slate-400 uppercase">Visitors</span>
          <div class="visitor-counter flex gap-1" id="visitor-count-box">
            <!-- Dynamically filled -->
            <span class="text-blue-500 font-mono font-bold">...</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
  <!--Footer Ends-->
  <!-- 📽️ Modern Video Player Modal -->
  <div id="videoModal" class="fixed inset-0 z-[10000] hidden opacity-0 transition-all duration-500 overflow-hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-950/95 backdrop-blur-2xl"></div>

    <!-- Modal Content -->
    <div class="relative w-full h-full flex flex-col items-center justify-center p-2 sm:p-8">
      <!-- Close Button -->
      <button id="closeVideoModal"
        class="absolute top-4 right-4 sm:top-6 sm:right-6 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-slate-800/80 sm:bg-white/10 text-white flex items-center justify-center hover:bg-red-500 transition-all z-[10001] shadow-xl">
        <i class="bi bi-x-lg text-lg sm:text-xl"></i>
      </button>

      <!-- Player Container -->
      <div
        class="w-full sm:max-w-5xl h-auto max-h-[85vh] sm:max-h-[80vh] bg-black rounded-[1.5rem] sm:rounded-[2rem] overflow-hidden shadow-[0_0_100px_rgba(37,99,235,0.3)] border border-white/5 relative group flex items-center justify-center">
        <video id="mainPlayer" class="w-full h-full max-h-[85vh] sm:max-h-[80vh] object-contain" controls autoplay
          playsinline>
          <source src="" type="video/mp4">
        </video>

        <!-- Bottom Info Bar (Better Mobile Visibility) -->
        <div
          class="absolute bottom-0 inset-x-0 p-4 sm:p-6 bg-gradient-to-t from-black via-black/40 to-transparent opacity-0 group-hover:opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity pointer-events-none">
          <h3 id="modalVideoTitle"
            class="text-white font-black text-lg sm:text-2xl uppercase tracking-tight sm:tracking-tighter">Video Title
          </h3>
          <p class="text-orange-500 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1">Campus Highlights •
            Kitabghar Live</p>
        </div>
      </div>
    </div>
  </div>

  <script src="js\index.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {

      // Video Modal Logic
      const videoTriggers = document.querySelectorAll('.video-trigger');
      const videoModal = document.getElementById('videoModal');
      const mainPlayer = document.getElementById('mainPlayer');
      const closeVideoBtn = document.getElementById('closeVideoModal');
      const modalTitle = document.getElementById('modalVideoTitle');

      videoTriggers.forEach(trigger => {
        trigger.addEventListener('click', function () {
          const videoSrc = this.getAttribute('data-video');
          const title = this.getAttribute('data-title');

          mainPlayer.src = videoSrc;
          modalTitle.textContent = title;

          videoModal.classList.remove('hidden');
          setTimeout(() => {
            videoModal.classList.remove('opacity-0');
          }, 10);
          mainPlayer.play();
        });
      });

      function closeVideo() {
        videoModal.classList.add('opacity-0');
        setTimeout(() => {
          videoModal.classList.add('hidden');
          mainPlayer.pause();
          mainPlayer.src = "";
        }, 500);
      }

      if (closeVideoBtn) closeVideoBtn.addEventListener('click', closeVideo);

      // Close on backdrop click (optional but good)
      videoModal.addEventListener('click', (e) => {
        if (e.target === videoModal || e.target.classList.contains('bg-slate-950/95')) {
          closeVideo();
        }
      });

      // 1. Sidebar Toggle ... (rest of search/replace starts here)
      window.toggleSidebar = function () {
        let sidebar = document.getElementById("sidebar");
        let overlay = document.getElementById("overlay");

        sidebar.classList.toggle("-translate-x-full");

        if (!sidebar.classList.contains("-translate-x-full")) {
          // Opened
          overlay.classList.remove("hidden");
          setTimeout(() => {
            overlay.classList.remove("opacity-0");
          }, 10);
        } else {
          // Closed
          overlay.classList.add("opacity-0");
          setTimeout(() => {
            overlay.classList.add("hidden");
          }, 300);
        }
      }

      // 2. Loader with Fade Out
      window.addEventListener("load", () => {
        const loader = document.getElementById("loader");
        if (loader) {
          loader.style.opacity = "0";
          setTimeout(() => {
            loader.style.display = "none";
          }, 500);
        }
      });

      // 3. Modal Functionality
      // 3. Modal Functionality (Fixed)
      const modal = document.getElementById("announcementModal");
      const closeBtn = document.getElementById("closeModal");
      const closeBtnBottom = document.getElementById("closeModalBtn");

      // Use sessionStorage so it shows once per browser session, not forever hidden
      if (modal && !sessionStorage.getItem("modalShown")) {
        setTimeout(() => {
          modal.classList.remove("hidden");
          // Small delay to allow display:block to apply before opacity transition
          setTimeout(() => {
            modal.classList.remove("opacity-0");
            const modalContent = modal.querySelector('div');
            if (modalContent) {
              modalContent.classList.remove("scale-95");
              modalContent.classList.add("scale-100");
            }
          }, 50);
          sessionStorage.setItem("modalShown", "true");
        }, 1500); // Delay appearance slightly
      }

      function closeModalFunc() {
        if (!modal) return;
        modal.classList.add("opacity-0");
        const modalContent = modal.querySelector('div');
        if (modalContent) {
          modalContent.classList.remove("scale-100");
          modalContent.classList.add("scale-95");
        }
        setTimeout(() => {
          modal.classList.add("hidden");
        }, 300);
      }

      if (closeBtn) closeBtn.onclick = closeModalFunc;
      if (closeBtnBottom) closeBtnBottom.onclick = closeModalFunc;

      window.onclick = function (event) {
        if (event.target === modal) {
          closeModalFunc();
        }
      };

      // 4. Video Play/Pause Auto
      const videoItems = document.querySelectorAll(".video-item video");
      videoItems.forEach((video) => {
        video.parentElement.addEventListener("mouseenter", () => {
          video.muted = false;
          video.play();
        });
        video.parentElement.addEventListener("mouseleave", () => {
          video.pause();
        });
      });

      // 5. Swiper Initialization
      if (typeof Swiper !== 'undefined') {
        const swiper = new Swiper(".swiper", {
          direction: "horizontal",
          loop: true,
          effect: "fade",
          fadeEffect: { crossFade: true },
          autoplay: {
            delay: 3500,
            disableOnInteraction: false,
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
        });

        // 🖼️ New Gallery Swiper (Coverflow Effect)
        const gallerySwiper = new Swiper(".gallerySwiper", {
          effect: "coverflow",
          grabCursor: true,
          centeredSlides: true,
          slidesPerView: "auto",
          coverflowEffect: {
            rotate: 30,
            stretch: 0,
            depth: 200,
            modifier: 1,
            slideShadows: true,
          },
          loop: true,
          autoplay: {
            delay: 3000,
            disableOnInteraction: false,
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
        });
      }

      // 🖼️ Gallery Modal Logic
      const mgModal = document.getElementById("fullGalleryModal");
      const mgContent = document.getElementById("fullGalleryContent");
      const mgOpenBtn = document.getElementById("openFullGallery");

      window.showFullGallery = () => {
        const mgModal = document.getElementById("fullGalleryModal");
        const mgContent = document.getElementById("fullGalleryContent");
        mgModal.classList.remove("hidden");
        setTimeout(() => {
          mgContent.classList.remove("scale-90", "opacity-0");
          mgContent.classList.add("scale-100", "opacity-100");
        }, 10);
        document.body.style.overflow = "hidden"; // Prevent scroll
      };

      window.closeFullGallery = () => {
        const mgModal = document.getElementById("fullGalleryModal");
        const mgContent = document.getElementById("fullGalleryContent");
        mgContent.classList.remove("scale-100", "opacity-100");
        mgContent.classList.add("scale-90", "opacity-0");
        setTimeout(() => {
          mgModal.classList.add("hidden");
          document.body.style.overflow = ""; // Re-enable scroll
        }, 400);
      };

      // --- Image Repository Data & Enhanced Preview logic ---
      const galleryData = <?php echo json_encode($gallery); ?>;
      let currentPreviewIdx = 0;

      window.updatePreviewContent = () => {
        const prImg = document.getElementById("previewImg");
        const prTitle = document.getElementById("previewTitle");
        const item = galleryData[currentPreviewIdx];

        // Add a small fade effect during change
        prImg.style.opacity = "0";
        setTimeout(() => {
          prImg.src = 'img/' + item.image_path;
          prTitle.textContent = item.title;
          prImg.style.opacity = "1";
        }, 150);
      };

      window.nextImage = (e) => {
        if (e) e.stopPropagation();
        currentPreviewIdx = (currentPreviewIdx + 1) % galleryData.length;
        updatePreviewContent();
      };

      window.prevImage = (e) => {
        if (e) e.stopPropagation();
        currentPreviewIdx = (currentPreviewIdx - 1 + galleryData.length) % galleryData.length;
        updatePreviewContent();
      };

      window.openPreview = (index) => {
        const prModal = document.getElementById("imageDetailModal");
        const prContent = document.getElementById("previewContent");

        currentPreviewIdx = index;
        updatePreviewContent();

        prModal.classList.remove("hidden");
        setTimeout(() => {
          prContent.classList.remove("scale-95", "opacity-0");
          prContent.classList.add("scale-100", "opacity-100");
        }, 10);
        document.body.style.overflow = "hidden";
      };

      window.closePreview = () => {
        const prModal = document.getElementById("imageDetailModal");
        const prContent = document.getElementById("previewContent");
        const mgModal = document.getElementById("fullGalleryModal");

        prContent.classList.remove("scale-100", "opacity-100");
        prContent.classList.add("scale-95", "opacity-0");
        setTimeout(() => {
          prModal.classList.add("hidden");
          if (!mgModal || mgModal.classList.contains("hidden")) {
            document.body.style.overflow = "";
          }
        }, 300);
      };

      if (mgOpenBtn) {
        mgOpenBtn.addEventListener("click", showFullGallery);
      }

      // 6. Visitor Counter
      // 6. Visitor Counter (Fixed & Modernized)
      const countBox = document.getElementById("visitor-count-box");

      if (countBox) {
        const countSpan = countBox.querySelector("span");
        let targetCount = <?php echo isset($visitor_count) ? (int) $visitor_count : 1234; ?>;
        if (targetCount === 0) targetCount = 1234; // Fallback

        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              startCounting();
              observer.unobserve(entry.target);
            }
          });
        }, { threshold: 0.5 });

        observer.observe(countBox);

        function startCounting() {
          let startTimestamp = null;
          const duration = 2000;

          const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const current = Math.floor(progress * targetCount);
            if (countSpan) countSpan.textContent = current.toLocaleString();
            else countBox.textContent = current.toLocaleString();

            if (progress < 1) {
              window.requestAnimationFrame(step);
            } else {
              if (countSpan) countSpan.textContent = targetCount.toLocaleString();
            }
          };
          window.requestAnimationFrame(step);
        }
      }

      // 7. Chatbot Logic
      const chatbotBtn = document.getElementById("chatbot-btn");
      const chatbotContainer = document.getElementById("chatbot-container");
      const closeChatbotBtn = document.getElementById("close-chatbot");

      const closeLabelBtn = document.getElementById("close-label-btn");
      const chatbotLabel = document.getElementById("chatbot-label");

      const chatbox = document.getElementById("chatbox");
      const userInput = document.getElementById("user-input");
      const sendBtn = document.getElementById("send-btn");
      const clearBtn = document.getElementById("clear-btn");

      // Quick Menu Logic
      const toggleMenuBtn = document.getElementById("toggle-quick-menu");
      const quickMenu = document.getElementById("quick-menu");
      const closeMenuBtn = document.getElementById("close-quick-menu");

      if (toggleMenuBtn && quickMenu) {
        toggleMenuBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          if (quickMenu.classList.contains("hidden")) {
            quickMenu.classList.remove("hidden");
            setTimeout(() => {
              quickMenu.classList.remove("opacity-0", "scale-95");
              quickMenu.classList.add("opacity-100", "scale-100");
            }, 10);
          } else {
            quickMenu.classList.remove("opacity-100", "scale-100");
            quickMenu.classList.add("opacity-0", "scale-95");
            setTimeout(() => {
              quickMenu.classList.add("hidden");
            }, 300);
          }
        });
      }

      if (closeMenuBtn && quickMenu) {
        closeMenuBtn.addEventListener("click", () => {
          quickMenu.classList.remove("opacity-100", "scale-100");
          quickMenu.classList.add("opacity-0", "scale-95");
          setTimeout(() => {
            quickMenu.classList.add("hidden");
          }, 300);
        });
      }

      // Close Quick Menu on Outside Click
      document.addEventListener("click", (e) => {
        if (quickMenu && !quickMenu.contains(e.target) && !toggleMenuBtn.contains(e.target)) {
          if (!quickMenu.classList.contains("hidden")) {
            quickMenu.classList.remove("opacity-100", "scale-100");
            quickMenu.classList.add("opacity-0", "scale-95");
            setTimeout(() => {
              quickMenu.classList.add("hidden");
            }, 300);
          }
        }
      });

      // Label Close
      if (closeLabelBtn && chatbotLabel) {
        closeLabelBtn.addEventListener("click", (e) => {
          e.stopPropagation();
          chatbotLabel.style.display = "none";
        });
      }

      // Toggle Chatbot
      if (chatbotBtn && chatbotContainer) {
        chatbotBtn.addEventListener("click", () => {
          if (chatbotContainer.classList.contains("hidden")) {
            chatbotContainer.classList.remove("hidden");
            chatbotContainer.classList.add("flex");
            if (userInput) setTimeout(() => userInput.focus(), 100);
            // Hide label if open
            if (chatbotLabel) chatbotLabel.style.display = "none";
          } else {
            chatbotContainer.classList.add("hidden");
            chatbotContainer.classList.remove("flex");
          }
        });
      }

      // Close Chatbot
      if (closeChatbotBtn && chatbotContainer) {
        closeChatbotBtn.addEventListener("click", () => {
          chatbotContainer.classList.add("hidden");
          chatbotContainer.classList.remove("flex");
        });
      }

      // Send Message
      if (sendBtn && userInput && chatbox) {
        const sendMessage = () => {
          const msg = userInput.value.trim();
          if (!msg) return;

          // User Msg
          const userDiv = document.createElement("div");
          userDiv.className = "flex items-start gap-4 justify-end user-message animate-fade-in-up";
          userDiv.innerHTML = `
                  <div class="bg-blue-600/90 backdrop-blur-md p-4 rounded-[2rem] rounded-tr-none shadow-xl text-white text-[15px] leading-relaxed border border-white/10 max-w-[85%]">
                    ${msg}
                  </div>
                  <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl shadow-sm shrink-0 border border-white/10">👤</div>
               `;
          chatbox.appendChild(userDiv);
          chatbox.scrollTop = chatbox.scrollHeight;
          userInput.value = "";


          // Show Typing Indicator
          const typingIndicator = document.getElementById("typing-indicator");
          if (typingIndicator) {
            typingIndicator.classList.remove("hidden");
            typingIndicator.classList.add("flex");
            chatbox.appendChild(typingIndicator); // Keep it at the bottom
            chatbox.scrollTop = chatbox.scrollHeight;
          }

          // Send to Backend
          const formData = new FormData();
          formData.append('message', msg);

          fetch('admin/php/chatbot_api.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.text())
            .then(data => {
              // Hide Typing Indicator
              if (typingIndicator) {
                typingIndicator.classList.add("hidden");
                typingIndicator.classList.remove("flex");
              }

              const botDiv = document.createElement("div");
              botDiv.className = "flex items-start gap-4 bot-message animate-fade-in-up group";
              botDiv.innerHTML = `
                  <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl shadow-sm shrink-0 border border-white/10 group-hover:bg-white/20 transition-colors">🤖</div>
                  <div class="bg-white/10 backdrop-blur-md p-4 rounded-[2rem] rounded-tl-none border border-white/10 text-slate-100 text-base leading-relaxed shadow-xl max-w-[85%]">
                      ${data}
                  </div>
                 `;
              chatbox.appendChild(botDiv);
              chatbox.scrollTop = chatbox.scrollHeight;
            })
            .catch(error => {
              // Hide Typing Indicator
              if (typingIndicator) {
                typingIndicator.classList.add("hidden");
                typingIndicator.classList.remove("flex");
              }

              const botDiv = document.createElement("div");
              botDiv.className = "flex items-start gap-4 bot-message animate-fade-in-up";
              botDiv.innerHTML = `
                  <div class="w-10 h-10 rounded-2xl bg-red-500/20 flex items-center justify-center text-xl shadow-sm shrink-0 border border-red-500/20">⚠️</div>
                  <div class="bg-red-500/10 backdrop-blur-md p-4 rounded-[2rem] rounded-tl-none border border-red-500/20 text-red-100 text-[15px] leading-relaxed shadow-xl">
                      Connection error. Please try again.
                  </div>
                 `;
              chatbox.appendChild(botDiv);
              chatbox.scrollTop = chatbox.scrollHeight;
            });

        }


        sendBtn.addEventListener("click", sendMessage);
        userInput.addEventListener("keypress", (e) => {
          if (e.key === "Enter") sendMessage();
        });

        // Global Command Function for Chatbot Buttons
        window.sendCommand = function (cmd) {
          userInput.value = cmd;
          sendMessage();
          // Close quick menu if open
          const quickMenu = document.getElementById("quick-menu");
          if (quickMenu && !quickMenu.classList.contains("hidden")) {
            quickMenu.classList.remove("opacity-100", "scale-100");
            quickMenu.classList.add("opacity-0", "scale-95");
            setTimeout(() => {
              quickMenu.classList.add("hidden");
            }, 300);
          }
        };
      }

      // Clear Chat
      if (clearBtn && chatbox) {
        clearBtn.addEventListener("click", () => {
          while (chatbox.children.length > 1) {
            chatbox.removeChild(chatbox.lastChild);
          }
        });
      }

      // 8. Cookie Popup Auto-Show
      if (!localStorage.getItem("cookiesAccepted")) {
        const popup = document.getElementById("cookiePopup");
        if (popup) {
          setTimeout(() => {
            popup.classList.remove("hidden");
          }, 3000); // Show after 3 seconds
        }
      }

      // 9. Font Resizer
      const increaseBtn = document.getElementById("increase-font");
      const decreaseBtn = document.getElementById("decrease-font");
      const resetBtn = document.getElementById("reset-font");

      // Load saved scale
      let scaleFactor = parseFloat(localStorage.getItem("scaleFactor")) || 1;
      function applyScale(factor) {
        document.documentElement.style.fontSize = (factor * 100) + "%";
        localStorage.setItem("scaleFactor", factor);
      }
      if (scaleFactor !== 1) applyScale(scaleFactor);

      if (increaseBtn) increaseBtn.onclick = () => { if (scaleFactor < 1.3) applyScale(scaleFactor += 0.05); }
      if (decreaseBtn) decreaseBtn.onclick = () => { if (scaleFactor > 0.8) applyScale(scaleFactor -= 0.05); }
      if (resetBtn) resetBtn.onclick = () => { scaleFactor = 1; applyScale(1); }

      // 10. Scroll to Top Button
      const scrollTopBtn = document.getElementById("scrollTopBtn");
      if (scrollTopBtn) {
        window.addEventListener("scroll", () => {
          if (window.scrollY > 300) {
            scrollTopBtn.classList.remove("hidden");
            scrollTopBtn.classList.add("flex");
          } else {
            scrollTopBtn.classList.add("hidden");
            scrollTopBtn.classList.remove("flex");
          }
        });
        scrollTopBtn.addEventListener("click", () => {
          window.scrollTo({ top: 0, behavior: "smooth" });
        });
      }

      // 11. Form Submission Spinners
      const formsWithSpinners = [
        { id: 'contribution-section-parallax', btn: 'contributeSubmitBtn', text: 'contributeBtnText', msg: 'Uploading Notes...', spinner: 'contributeBtnSpinner' },
        { id: 'contactSubmitBtn', isBtn: true, btn: 'contactSubmitBtn', text: 'contactBtnText', msg: 'Sending...', spinner: 'contactBtnSpinner' }
      ];

      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function (e) {
          const contributeBtn = document.getElementById('contributeSubmitBtn');
          if (this.action.includes('contribute_notes.php')) {
            contributeBtn.disabled = true;
            document.getElementById('contributeBtnText').textContent = 'Uploading Notes...';
            document.getElementById('contributeBtnSpinner').classList.remove('hidden');
          }
          if (this.action.includes('contact.php')) {
            const btn = document.getElementById('contactSubmitBtn');
            btn.disabled = true;
            document.getElementById('contactBtnText').textContent = 'Sending...';
            document.getElementById('contactBtnSpinner').classList.remove('hidden');
          }
        });
      });
    });

    // --- Global Helper Functions ---

    // Cookie Accept
    function acceptCookies() {
      localStorage.setItem("cookiesAccepted", "true");
      let popup = document.getElementById("cookiePopup");
      if (popup) popup.classList.add("hidden");
    }

    // Toast Notification
    function showToast(message, type = 'success') {
      // Logic for user requested specific toast (conrtibution form)
      if (!message && !type) {
        const specificToast = document.getElementById('toastContributeNotes');
        if (specificToast) {
          specificToast.classList.remove('hidden');
          setTimeout(() => { specificToast.classList.add('hidden'); }, 3000);
        }
        return;
      }

      // Generic Toast
      const container = document.getElementById('toast-container');
      if (!container) return;

      const toast = document.createElement('div');
      toast.className = `transform transition-all duration-300 translate-y-2 opacity-0 flex items-start gap-4 bg-white dark:bg-slate-800 border-l-4 ${type === 'success' ? 'border-green-500' : 'border-red-500'} shadow-2xl rounded-xl p-4`;
      toast.innerHTML = `
        <div class="text-2xl">${type === 'success' ? '✅' : '❌'}</div>
        <div>
          <h4 class="font-bold text-slate-800 dark:text-white">${type === 'success' ? 'Success' : 'Error'}</h4>
          <p class="text-sm text-slate-600 dark:text-slate-300">${message}</p>
        </div>
      `;

      container.appendChild(toast);

      // Animate In
      requestAnimationFrame(() => {
        toast.classList.remove("translate-y-2", "opacity-0");
      });

      setTimeout(() => {
        toast.classList.add("opacity-0", "translate-x-full");
        setTimeout(() => toast.remove(), 300);
      }, 5000);
    }

    // Google Translate Init
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({ pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE }, 'google_translate_element');
    }
  </script>


  <!-- Scripts -->
  <script type="text/javascript"
    src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init({
      once: true,
      offset: 100,
      duration: 800,
    });

    // 🍂 Seasonal Effects System (Indian Seasons - IMD Style)
    document.addEventListener("DOMContentLoaded", function () {
      const month = new Date().getMonth(); // 0 = Jan, 11 = Dec
      const effectContainer = document.createElement("div");
      effectContainer.id = "seasonal-effect";
      effectContainer.className = "fixed inset-0 pointer-events-none z-[1] transition-all duration-1000";
      document.body.appendChild(effectContainer);

      // Season Logic:
      // Jan(0)-Apr(3): Winter (Cool, Mist)
      // May(4)-Jun(5): Summer (Warm, Heat)
      // Jul(6)-Sep(8): Monsoon (Rain, Green/Blue)
      // Oct(9): Post-Monsoon (Clear, Breezy)
      // Nov(10)-Dec(11): Early Winter (Muted, Chill)

      let season = "";

      if (month >= 0 && month <= 3) season = "winter";
      else if (month >= 4 && month <= 5) season = "summer";
      else if (month >= 6 && month <= 8) season = "monsoon";
      else if (month === 9) season = "post-monsoon";
      else season = "early-winter";

      console.log(`Current Season Theme: ${season}`);

      if (season === "winter") {
        // ❄️ Winter: Clean, Crisp, Mist
        // Subtle blue tint at the top
        effectContainer.classList.add("bg-gradient-to-b", "from-blue-50/20", "via-transparent", "to-transparent");
        effectContainer.innerHTML = `
                <style>
                    .mist-particle {
                        position: absolute;
                        background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, transparent 70%);
                        width: 150px;
                        height: 150px;
                        border-radius: 50%;
                        filter: blur(20px);
                        opacity: 0;
                        animation: floatMist 15s infinite ease-in-out;
                    }
                    @keyframes floatMist {
                        0% { transform: translate(0, 0) scale(1); opacity: 0; }
                        50% { opacity: 0.3; }
                        100% { transform: translate(50px, -50px) scale(1.5); opacity: 0; }
                    }
                </style>
            `;
        // Spawn mist
        for (let i = 0; i < 6; i++) {
          const mist = document.createElement("div");
          mist.className = "mist-particle";
          mist.style.left = Math.random() * 90 + "%";
          mist.style.top = Math.random() * 80 + "%";
          mist.style.animationDelay = Math.random() * 5 + "s";
          effectContainer.appendChild(mist);
        }
      }
      else if (season === "summer") {
        // ☀️ Summer: Warm, energetic
        // Warm orange/yellow overlay top-right
        effectContainer.style.background = "radial-gradient(circle at top right, rgba(255, 165, 0, 0.08), transparent 40%)";
        effectContainer.innerHTML = `
                <style>
                   @keyframes shimmer {
                       0% { opacity: 0.3; transform: scale(1); }
                       50% { opacity: 0.6; transform: scale(1.02); }
                       100% { opacity: 0.3; transform: scale(1); }
                   }
                   .sun-glow {
                       position: fixed;
                       top: -100px;
                       right: -100px;
                       width: 500px;
                       height: 500px;
                       background: radial-gradient(circle, rgba(253, 224, 71, 0.2) 0%, transparent 70%);
                       filter: blur(60px);
                       animation: shimmer 8s infinite ease-in-out;
                   }
                </style>
                <div class="sun-glow"></div>
            `;
      }
      else if (season === "monsoon") {
        // 🌧️ Monsoon: Rain, lush feels
        effectContainer.classList.add("bg-gradient-to-t", "from-teal-50/5", "to-transparent");
        effectContainer.innerHTML = `
                <style>
                    .rain-line {
                        position: absolute;
                        background: linear-gradient(to bottom, transparent, rgba(94, 234, 212, 0.3));
                        width: 1px;
                        height: 40px;
                        top: -50px;
                        animation: rainFall 0.7s linear infinite;
                    }
                    @keyframes rainFall {
                        to { transform: translateY(110vh); }
                    }
                </style>
            `;
        for (let i = 0; i < 40; i++) { // Moderate rain, not overwhelming
          const drop = document.createElement("div");
          drop.className = "rain-line";
          drop.style.left = Math.random() * 100 + "vw";
          drop.style.animationDuration = (Math.random() * 0.5 + 0.5) + "s";
          drop.style.animationDelay = Math.random() * 2 + "s";
          effectContainer.appendChild(drop);
        }
      }
      else if (season === "post-monsoon" || season === "early-winter") {
        // 🍂 Post-Monsoon / Early Winter: Clear, Breeze
        const isEarlyWinter = season === "early-winter";
        if (isEarlyWinter) {
          effectContainer.classList.add("bg-gradient-to-b", "from-slate-200/10", "to-transparent"); // cool
        } else {
          effectContainer.classList.add("bg-gradient-to-b", "from-lime-50/10", "to-transparent"); // fresh
        }

        effectContainer.innerHTML = `
                 <style>
                    .breeze-particle {
                        position: absolute;
                        background-color: ${isEarlyWinter ? '#94a3b8' : '#84cc16'}; /* Grayish or Greenish */
                        width: ${isEarlyWinter ? '4px' : '6px'};
                        height: ${isEarlyWinter ? '4px' : '6px'};
                        border-radius: 50%;
                        opacity: 0.6;
                        animation: floatBreeze 12s linear infinite;
                    }
                     @keyframes floatBreeze {
                        0% { transform: translate(-10px, 100vh) rotate(0deg); opacity: 0; }
                        20% { opacity: 0.8; }
                        80% { opacity: 0.8; }
                        100% { transform: translate(20vw, -10px) rotate(360deg); opacity: 0; }
                    }
                </style>
            `;
        // Upward floating breeze particles
        for (let i = 0; i < 15; i++) {
          const part = document.createElement("div");
          part.className = "breeze-particle";
          part.style.left = Math.random() * 100 + "vw";
          part.style.animationDuration = Math.random() * 5 + 10 + "s";
          part.style.animationDelay = Math.random() * 5 + "s";
          effectContainer.appendChild(part);
        }
      }
    });
  </script>
  <!-- Toast Trigger Logic -->
  <?php
  $toast_msg = '';
  $toast_type = 'success';

  if (isset($_SESSION['toast'])) {
    $toast_msg = $_SESSION['toast']['message'];
    $toast_type = $_SESSION['toast']['type'];
    unset($_SESSION['toast']);
  } elseif (isset($_GET['toast'])) {
    if ($_GET['toast'] === 'success') {
      $toast_msg = "Message sent successfully!";
      $toast_type = 'success';
    } elseif ($_GET['toast'] === 'error') {
      $toast_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "An error occurred.";
      $toast_type = 'error';
    }
  }

  if ($toast_msg): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof showToast === 'function') {
        showToast("<?= $toast_msg ?>", "<?= $toast_type ?>");
      }
    });
  </script>
  <?php endif; ?>

  <!-- Connectivity Handler -->
  <script src="js/connectivity.js"></script>
</body>

</html>