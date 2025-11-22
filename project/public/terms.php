<?php
session_start();
$pageTitle = 'Terms and Conditions - SkillLink';
require_once '../src/includes/header.php';
?>

<div class="min-h-screen bg-white py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 text-center animate-fade-in">
            <h1 class="text-4xl font-bold text-black mb-3">Terms and Conditions</h1>
            <p class="text-gray-600">Last Updated: <?php echo date('F d, Y'); ?></p>
        </div>

        <div class="prose prose-lg max-w-none animate-slide-up">
            <div class="card p-8 space-y-6">
                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">1. Acceptance of Terms</h2>
                    <p class="text-gray-700 leading-relaxed">
                        By accessing and using SkillLink ("the Platform"), you accept and agree to be bound by the terms and provisions of this agreement. If you do not agree to these Terms and Conditions, please do not use the Platform.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">2. Use License</h2>
                    <p class="text-gray-700 leading-relaxed mb-3">
                        Permission is granted to temporarily access the materials (information or software) on SkillLink for personal, non-commercial use only. This is the grant of a license, not a transfer of title, and under this license you may not:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Modify or copy the materials</li>
                        <li>Use the materials for any commercial purpose or for any public display</li>
                        <li>Attempt to decompile or reverse engineer any software contained on SkillLink</li>
                        <li>Remove any copyright or other proprietary notations from the materials</li>
                        <li>Transfer the materials to another person or "mirror" the materials on any other server</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">3. User Accounts</h2>
                    <p class="text-gray-700 leading-relaxed mb-3">
                        When you create an account with us, you must provide accurate, complete, and current information. Failure to do so constitutes a breach of the Terms, which may result in immediate termination of your account.
                    </p>
                    <p class="text-gray-700 leading-relaxed">
                        You are responsible for safeguarding the password that you use to access the Platform and for any activities or actions under your password. You agree not to disclose your password to any third party.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">4. Content and Intellectual Property</h2>
                    <p class="text-gray-700 leading-relaxed">
                        All content on SkillLink, including but not limited to text, graphics, logos, images, and software, is the property of SkillLink or its content suppliers and is protected by international copyright laws. The compilation of all content on this site is the exclusive property of SkillLink.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">5. User Conduct</h2>
                    <p class="text-gray-700 leading-relaxed mb-3">
                        You agree to use the Platform only for lawful purposes and in a way that does not infringe the rights of, restrict, or inhibit anyone else's use and enjoyment of the Platform. Prohibited behavior includes:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li>Harassing or causing distress or inconvenience to any other user</li>
                        <li>Transmitting obscene or offensive content</li>
                        <li>Disrupting the normal flow of dialogue within the Platform</li>
                        <li>Attempting to gain unauthorized access to other users' accounts</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">6. Privacy Policy</h2>
                    <p class="text-gray-700 leading-relaxed">
                        Your use of SkillLink is also governed by our Privacy Policy. We collect and use your personal information in accordance with applicable data protection laws. By using the Platform, you consent to such processing and you warrant that all data provided by you is accurate.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">7. Disclaimer</h2>
                    <p class="text-gray-700 leading-relaxed">
                        The materials on SkillLink are provided on an 'as is' basis. SkillLink makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">8. Limitations</h2>
                    <p class="text-gray-700 leading-relaxed">
                        In no event shall SkillLink or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on SkillLink, even if SkillLink or a SkillLink authorized representative has been notified orally or in writing of the possibility of such damage.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">9. Modifications</h2>
                    <p class="text-gray-700 leading-relaxed">
                        SkillLink may revise these Terms and Conditions at any time without notice. By using this Platform, you are agreeing to be bound by the then-current version of these Terms and Conditions.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">10. Governing Law</h2>
                    <p class="text-gray-700 leading-relaxed">
                        These terms and conditions are governed by and construed in accordance with the laws of your jurisdiction, and you irrevocably submit to the exclusive jurisdiction of the courts in that location.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-black mb-4">11. Contact Information</h2>
                    <p class="text-gray-700 leading-relaxed">
                        If you have any questions about these Terms and Conditions, please contact us through the Platform's support channels.
                    </p>
                </section>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        By creating an account on SkillLink, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.
                    </p>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="register.php" class="btn btn-primary">Create Account</a>
                <a href="index.php" class="btn btn-secondary ml-3">Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../src/includes/footer.php'; ?>

