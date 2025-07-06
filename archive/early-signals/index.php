<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Weather Alert</title>
    <style>
        body {
            background-color: #f0f0f0;
            padding: 20px;
            margin: 0;
        }

        .emergency-weather-alert {
            border-radius: 12px;
            margin: 20px 0;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-left: 6px solid;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            animation: alertPulse 2s ease-in-out infinite alternate;
        }

        .alert-severe {
            background: linear-gradient(135deg, #ff4444, #cc0000);
            border-left-color: #990000;
            color: white;
        }

        .alert-moderate {
            background: linear-gradient(135deg, #ff8800, #e65100);
            border-left-color: #bf360c;
            color: white;
        }

        .alert-minor {
            background: linear-gradient(135deg, #ffaa00, #ff6f00);
            border-left-color: #e65100;
            color: white;
        }

        .alert-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .alert-icon {
            width: 48px;
            height: 48px;
            margin-right: 15px;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
        }

        .alert-icon svg {
            width: 100%;
            height: 100%;
        }

        .alert-title {
            flex: 1;
            margin: 0;
            font-size: 1.8em;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .alert-time {
            font-size: 0.9em;
            opacity: 0.9;
            font-weight: 500;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 15px;
            margin-left: auto;
        }

        .alert-content {
            line-height: 1.6;
        }

        .alert-description {
            font-size: 1.1em;
            margin-bottom: 15px;
            text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
        }

        .alert-instructions {
            background: rgba(255,255,255,0.15);
            padding: 15px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .alert-instructions h3 {
            margin: 0 0 10px 0;
            font-size: 1.2em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @keyframes alertPulse {
            0% { transform: scale(1); box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
            100% { transform: scale(1.02); box-shadow: 0 6px 25px rgba(0,0,0,0.25); }
        }

        @media (max-width: 768px) {
            .alert-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .alert-time {
                margin-left: 0;
                margin-top: 10px;
            }

            .alert-title {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>
    <!-- Severe Alert Example -->
    <div class='emergency-weather-alert alert-severe'>
        <div class='alert-header'>
            <div class='alert-icon'>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 8h14l-2-2h-8l-2 2h-2zm2 2l2 2h8l2-2H5zm3 4l1 1h6l1-1H8zm2 3l1 1h2l1-1h-4z"/></svg>
            </div>
            <h2 class='alert-title'>Landslide Warning</h2>
            <span class='alert-time'>Jan 15, 2024 3:45 PM</span>
        </div>
        <div class='alert-content'>
            <p class='alert-description'>A landslide warning has been issued for your area. Take immediate shelter in a sturdy building on the lowest floor, away from windows.</p>
            <div class='alert-instructions'>
                <h3>What to do:</h3>
                <p>Move to an interior room on the lowest floor of a sturdy building. Avoid windows and stay away from large roof spans. If outdoors, lie flat in a nearby ditch or depression and cover your head with your hands.</p>
            </div>
        </div>
    </div>

    <!-- Moderate Alert Example -->
    <div class='emergency-weather-alert alert-moderate'>
        <div class='alert-header'>
            <div class='alert-icon'>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 16l2.5-3.5-1.5-2 3-4.5h4l-2 3h3l-4.5 6-2.5-2.5L6 16z"/><path d="M19 12c0-3.31-2.69-6-6-6-.55 0-1.08.08-1.59.23-.51-1.93-2.24-3.23-4.41-3.23-2.76 0-5 2.24-5 5 0 .28.02.56.07.83C.88 9.66 0 10.69 0 12c0 1.66 1.34 3 3 3h13c1.66 0 3-1.34 3-3z"/></svg>
            </div>
            <h2 class='alert-title'>Typhoon Warning</h2>
            <span class='alert-time'>Jan 15, 2024 2:15 PM</span>
        </div>
        <div class='alert-content'>
            <p class='alert-description'>A typhoon with damaging winds and heavy rainfall is approaching your area this afternoon.</p>
            <div class='alert-instructions'>
                <h3>What to do:</h3>
                <p>Stay indoors and away from windows. Avoid using electrical appliances and plumbing. If driving, pull over safely and wait for the storm to pass.</p>
            </div>
        </div>
    </div>

    <!-- Minor Alert Example -->
    <div class='emergency-weather-alert alert-minor'>
        <div class='alert-header'>
            <div class='alert-icon'>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M6 3l2 3 2-3 2 3 2-3 2 3 2-3v6c0 .55-.45 1-1 1s-1-.45-1-1-.45-1-1-1-1 .45-1 1-.45 1-1 1-1-.45-1-1-.45-1-1-1-1 .45-1 1-.45 1-1 1-1-.45-1-1V3z"/><path d="M2 14c0-.55.45-1 1-1s1 .45 1 1-.45 1-1 1-1-.45-1-1zm4 0c0-.55.45-1 1-1s1 .45 1 1-.45 1-1 1-1-.45-1-1zm4 0c0-.55.45-1 1-1s1 .45 1 1-.45 1-1 1-1-.45-1-1zm4 0c0-.55.45-1 1-1s1 .45 1 1-.45 1-1 1-1-.45-1-1zm4 0c0-.55.45-1 1-1s1 .45 1 1-.45 1-1 1-1-.45-1-1z"/></svg>
            </div>
            <h2 class='alert-title'>Flash Flood Advisory</h2>
            <span class='alert-time'>Jan 15, 2024 1:30 PM</span>
        </div>
        <div class='alert-content'>
            <p class='alert-description'>Minor flooding in low-lying and poor drainage areas is expected due to heavy rainfall.</p>
        </div>
    </div>
</body>
</html>
