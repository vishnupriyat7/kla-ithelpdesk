<div class="custom-login-container">
<style>
    /* Override Filament layout wrappers to allow full-screen split design */
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        background-color: #111827 !important; /* bg-gray-900 */
    }
    .fi-layout, .fi-main {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        height: 100vh !important;
    }
    .fi-main-ctn {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Full height container */
    .custom-login-container {
        display: flex;
        min-height: 100vh;
        width: 100vw;
        background-color: #111827; /* gray-900 */
    }

    /* Left pane (Image) */
    .custom-login-left {
        display: none;
        position: relative;
        overflow: hidden;
        background-color: #111827;
    }
    @media (min-width: 1024px) {
        .custom-login-left {
            display: block;
            width: 60%;
        }
    }
    .custom-login-bg {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    .custom-login-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(17,24,39,0.4), rgba(17,24,39,0.95));
    }
    .custom-login-text {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 0 3rem;
        z-index: 10;
    }
    @media (min-width: 1024px) {
        .custom-login-text { padding: 0 6rem; }
    }
    .custom-title {
        font-size: 3rem;
        line-height: 1;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.025em;
        margin-bottom: 1rem;
        text-shadow: 0 4px 20px rgba(0,0,0,0.5);
    }
    @media (min-width: 1024px) {
        .custom-title { font-size: 4.5rem; }
    }
    .custom-subtitle {
        font-size: 1.25rem;
        color: #a5f3fc; /* cyan-200 */
        font-weight: 300;
        max-width: 32rem;
        line-height: 1.625;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }
    @media (min-width: 1024px) {
        .custom-subtitle { font-size: 1.5rem; }
    }

    /* Right pane (Form) */
    .custom-login-right {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background-color: #111827;
        z-index: 20;
        border-left: 1px solid #1f2937;
    }
    @media (min-width: 1024px) {
        .custom-login-right { width: 40%; }
    }
    .custom-login-card {
        width: 100%;
        max-width: 28rem;
        padding: 2.5rem;
        background-color: rgba(31, 41, 55, 0.5); /* bg-gray-800/50 */
        backdrop-filter: blur(12px);
        border-radius: 1rem;
        border: 1px solid #374151; /* border-gray-700 */
        box-shadow: 0 0 40px rgba(0,0,0,0.3);
    }
    .custom-login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .custom-login-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
        border-radius: 9999px;
        background-color: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .custom-login-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.5rem;
    }
    .custom-login-desc {
        color: #9ca3af; /* gray-400 */
    }

    /* Target Filament form fields to ensure they look good on dark mode */
    .fi-fo-field-wrp-label,
    .fi-fo-field-wrp-label span {
        color: #e5e7eb !important; /* text-gray-200 */
    }
    .fi-input-wrp {
        background-color: rgba(31, 41, 55, 0.8) !important; /* bg-gray-800 */
        border-color: #4b5563 !important; /* border-gray-600 */
        color: #ffffff !important;
    }
    .fi-input {
        color: #ffffff !important;
        background-color: transparent !important;
    }
    .fi-input::placeholder {
        color: #9ca3af !important;
    }
    
    /* Checkbox for Remember Me */
    .fi-checkbox-input {
        background-color: rgba(31, 41, 55, 0.8) !important;
        border-color: #4b5563 !important;
    }
    .fi-checkbox-input:checked {
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }
    .fi-fo-checkbox-label,
    .fi-fo-checkbox-label span,
    label[for="data.remember"],
    label[for="data.remember"] span,
    .justify-end span {
        color: #e5e7eb !important;
    }

    /* Any generic label inside the form */
    .custom-login-card form label,
    .custom-login-card form label span {
        color: #e5e7eb !important;
    }
</style>
    <!-- Left side: Futuristic Image Background -->
    <div wire:ignore class="custom-login-left">
        <div class="custom-login-bg" style="background-image: url('{{ asset('images/login-bg.png') }}');"></div>
        <div class="custom-login-overlay"></div>
        
        <div class="custom-login-text">
            <h1 class="custom-title">
                IT HelpDesk
            </h1>
            <p class="custom-subtitle">
                Empowering your workflow with state-of-the-art technological support and seamless operations.
            </p>
        </div>
    </div>
    
    <!-- Right side: Login Form -->
    <div class="custom-login-right">
        <div class="custom-login-card">
            <div class="custom-login-header">
                <div class="custom-login-icon">
                    <svg style="width: 2rem; height: 2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h2 class="custom-login-title">Welcome Back</h2>
                <p class="custom-login-desc">Sign in to access the IT HelpDesk</p>
            </div>

            <x-filament-panels::form id="form" wire:submit="authenticate">
                {{ $this->form }}

                <div style="margin-top: 1.5rem;">
                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </div>
            </x-filament-panels::form>
        </div>
    </div>
</div>
