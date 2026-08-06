<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // `DB::table()` already applies the connection prefix.
        $table = 'faq_entries';

        // Clear existing FAQs so the seeder is idempotent in dev.
        DB::table($table)->delete();

        $lang = 'en';
        $rows = [];

        $add = function (string $title, string $content, int $weight, ?int $parentId = null) use (&$rows, $lang) {
            $rows[] = [
                'translation_lang' => $lang,
                'translation_of'    => null,
                'parent_id'         => $parentId,
                'faq_pid'           => 0,
                'faq_weight'        => $weight,
                'faq_title'         => $title,
                'faq_content'       => $content,
                'active'            => 1,
            ];
        };

        // ─── 1. Getting started ───────────────────────────────────────────
        $add('How do I create an eSawda account?',
            '<p>Click <strong>Get Started</strong> in the top-right corner, then sign up with your email, phone number, or Google account. You will receive a 6-digit verification code by SMS. Once verified, your account is ready — no fees, no commitment.</p>',
            10);

        $add('Is it free to list products on eSawda?',
            '<p>Yes. Posting a standard listing is completely free and stays live for 60 days. Optional promotions such as <em>bump-up</em>, <em>featured</em>, and <em>store subscriptions</em> are paid features that give your listing more visibility.</p>',
            20);

        $add('How do I verify my account?',
            '<p>Go to <strong>Dashboard → Settings → Verification</strong> and upload a clear photo of your NID (National ID) or passport. Our team reviews submissions within 24 hours. Verified sellers get a blue tick and appear higher in search results.</p>',
            30);

        // ─── 2. Buying ────────────────────────────────────────────────────
        $add('How do I contact a seller?',
            '<p>Open any listing and tap <strong>Chat with seller</strong>. eSawda&rsquo;s built-in messaging keeps your phone number private and lets you track conversations in <strong>Messages</strong>. We strongly recommend staying on-platform so our support team can help if something goes wrong.</p>',
            40);

        $add('How do I pay for a product?',
            '<p>eSawda itself does not process payments between buyers and sellers. You and the seller agree on the payment method in chat — cash on meet-up, bank transfer, mobile wallet (bKash/Nagad/Rocket), or any other method you both trust. Always meet in a public place and inspect the item before paying.</p>',
            50);

        $add('Can I return a product if it&rsquo;s not as described?',
            '<p>Returns are handled directly between buyer and seller based on whatever return policy the seller has published on the listing. If a seller refuses to resolve a clear misrepresentation, open a dispute from <strong>Messages → Report</strong> and our moderation team will step in.</p>',
            60);

        // ─── 3. Selling ───────────────────────────────────────────────────
        $add('How do I post my first ad?',
            '<p>From your dashboard, click <strong>Post Ad</strong>. Add up to 8 photos, a clear title, a description with key details (condition, model, year, etc.), a price, and a category. Listings with at least 3 photos and a complete description get 4× more enquiries.</p>',
            70);

        $add('How long does a listing stay active?',
            '<p>Standard listings stay live for <strong>60 days</strong>. After that they are auto-archived but remain in your dashboard. You can renew or repost a listing any time with one click.</p>',
            80);

        $add('What are featured listings and how do they work?',
            '<p>Featured listings appear at the top of search results and on the homepage for 7 days. You can upgrade any active listing to featured from your dashboard for a small fee. Average featured listings receive 3&ndash;5× more views.</p>',
            90);

        $add('Can I edit a listing after posting?',
            '<p>Yes. Go to <strong>Dashboard → My Ads</strong>, pick the listing, and click <strong>Edit</strong>. Photos, price, description, and category can all be updated. The listing will re-appear in search results after a quick re-moderation (usually &lt; 5 minutes).</p>',
            100);

        // ─── 4. Safety & trust ────────────────────────────────────────────
        $add('How does eSawda keep buyers and sellers safe?',
            '<p>Every listing goes through automated checks (duplicate images, price anomalies, banned keywords) plus a human review queue. We verify seller identities, moderate chat for scams and phishing links, and provide a dispute resolution team. <strong>Never</strong> share OTPs, send advance payments to strangers, or click suspicious links.</p>',
            110);

        $add('What should I do if I suspect a scam?',
            '<p>Report the listing or the user directly from the chat (⋮ menu → <strong>Report</strong>). Our trust &amp; safety team reviews every report within a few hours. For financial fraud, also contact your bank or mobile-wallet provider immediately.</p>',
            120);

        // ─── 5. Account & technical ───────────────────────────────────────
        $add('I forgot my password — how do I reset it?',
            '<p>On the sign-in page, click <strong>Forgot password</strong>, enter the email or phone on your account, and we&rsquo;ll send a reset link or code. The link expires in 30 minutes. If you no longer have access to the original email/phone, contact support with proof of identity.</p>',
            130);

        $add('How do I delete my account?',
            '<p>Go to <strong>Dashboard → Settings → Account → Delete account</strong>. We will archive your data for 30 days (in case you change your mind) and then permanently delete it. Active subscriptions must be cancelled before deletion.</p>',
            140);

        $add('Does eSawda have a mobile app?',
            '<p>Yes — Android and iOS apps are available. Search <em>eSawda</em> on the Play Store and App Store, or scan the QR code at the bottom of the homepage. The web version works on any modern browser; no install required.</p>',
            150);

        // ─── 6. For businesses ────────────────────────────────────────────
        $add('I run a shop — can I have a store page?',
            '<p>Absolutely. <strong>Store plans</strong> give you a branded storefront, bulk upload, analytics, and priority support. Plans start at BDT 999/month. See <a href="/membership">Membership</a> for the full comparison.</p>',
            160);

        $add('How do I contact eSawda support?',
            '<p>The fastest way is in-app chat (Mon&ndash;Sat, 9 AM&ndash;9 PM BST). You can also email <a href="mailto:support@esawda.com">support@esawda.com</a> or use the form on the <a href="/contact">Contact</a> page. Press and partnership enquiries go to <a href="mailto:press@esawda.com">press@esawda.com</a>.</p>',
            170);

        // Bulk insert
        DB::table($table)->insert($rows);
    }
}
