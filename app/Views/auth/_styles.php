    <style>
        .login-shell,
        .login-shell * { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        .login-kicker { color: #5279d9; font-size: .64rem; font-weight: 800; letter-spacing: .16em; line-height: 1; text-transform: uppercase; }
        .login-heading { color: #14243a; font-size: clamp(1.85rem, 3vw, 2.35rem); font-weight: 700; letter-spacing: -.065em; line-height: 1.1; }
        .login-subtitle { color: #7c899b; font-size: .78rem; line-height: 1.7; }
        .login-label { color: #26364d; font-size: .73rem; font-weight: 700; letter-spacing: -.01em; }
        .login-field { min-height: 3.15rem; border: 1px solid #dfe6ef; border-radius: .7rem; background: #fff; color: #17243a; font-size: .78rem; font-weight: 500; outline: none; transition: border-color .18s ease, box-shadow .18s ease; }
        .login-field:hover { border-color: #bccbe0; }
        .login-field:focus { border-color: #5279d9; box-shadow: 0 0 0 3px rgba(82,121,217,.12); }
        .login-field::placeholder { color: #a9b4c2; font-weight: 400; }
        .login-field.has-error { border-color: #e9a5a5; background: #fffafa; }
        .login-field.has-error:focus { box-shadow: 0 0 0 3px rgba(220,70,70,.1); }
        .login-icon { color: #91a0b2; transition: color .18s ease; }
        .login-field-wrap:focus-within .login-icon { color: #5279d9; }
        .login-submit { min-height: 3.15rem; border: 1px solid #162d49; border-radius: .7rem; background: #162d49; box-shadow: 0 8px 16px rgba(22,45,73,.14); font-size: .78rem; font-weight: 800; letter-spacing: -.01em; transition: background .18s ease, transform .18s ease, box-shadow .18s ease; }
        .login-submit:hover { background: #1d3b5e; transform: translateY(-1px); box-shadow: 0 11px 20px rgba(22,45,73,.2); }
        .login-submit:active { transform: translateY(0); }
        .login-submit:disabled { transform: none; box-shadow: none; }
        .login-security-note { display: flex; align-items: center; justify-content: center; gap: .4rem; color: #9aa6b6; font-size: .64rem; font-weight: 600; }
    </style>
