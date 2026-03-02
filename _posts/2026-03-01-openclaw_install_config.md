---
layout: post
title: OpenClaw Installation & Configuration on Linux
subtitle:
tags: [technical, AI, agent, LLM, Slack, automation]
author: Yuanpeng Zhang
comments: true
use_math: true
---

Here in this post, I am noting down the installation and configuration for `OpenClaw`. First, we follow the installation instructions [here](https://docs.openclaw.ai/install#macos-%2F-linux-%2F-wsl2) and carefully go through each step. Below is presented the whole input/output during my installation, where all the API keys and configuration tokens, etc. are omitted.

```
  🦞 OpenClaw Installer
  Hot reload for config, cold sweat for deploys.

✓ Detected: linux

Install plan
OS: linux
Install method: npm
Requested version: latest

[1/3] Preparing environment
· Node.js not found, installing it now
· Installing Node.js via NodeSource
· Installing Linux build tools (make/g++/cmake/python3)
✓ Build tools installed
✓ Node.js v22 installed
· Active Node.js: v22.22.0 (/usr/bin/node)
· Active npm: 10.9.4 (/usr/bin/npm)

[2/3] Installing OpenClaw
✓ Git already installed
· Configuring npm for user-local installs
✓ npm configured for user installs
· Installing OpenClaw v2026.2.26
✓ OpenClaw npm package installed
✓ OpenClaw installed

[3/3] Finalizing setup

! PATH missing npm global bin dir: /home/ubuntu/.npm-global/bin
  This can make openclaw show as "command not found" in new terminals.
  Fix (zsh: ~/.zshrc, bash: ~/.bashrc):
    export PATH="/home/ubuntu/.npm-global/bin:$PATH"

🦞 OpenClaw installed successfully (2026.2.26)!
Settled in. Time to automate your life whether you're ready or not.

· Starting setup


🦞 OpenClaw 2026.2.26 (bc50708) — curl for conversations.

▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄▄
██░▄▄▄░██░▄▄░██░▄▄▄██░▀██░██░▄▄▀██░████░▄▄▀██░███░██
██░███░██░▀▀░██░▄▄▄██░█░█░██░█████░████░▀▀░██░█░█░██
██░▀▀▀░██░█████░▀▀▀██░██▄░██░▀▀▄██░▀▀░█░██░██▄▀▄▀▄██
▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀▀
                  🦞 OPENCLAW 🦞

┌  OpenClaw onboarding
│
◇  Security ─────────────────────────────────────────────────────────────────────────────────╮
│                                                                                            │
│  Security warning — please read.                                                           │
│                                                                                            │
│  OpenClaw is a hobby project and still in beta. Expect sharp edges.                        │
│  By default, OpenClaw is a personal agent: one trusted operator boundary.                  │
│  This bot can read files and run actions if tools are enabled.                             │
│  A bad prompt can trick it into doing unsafe things.                                       │
│                                                                                            │
│  OpenClaw is not a hostile multi-tenant boundary by default.                               │
│  If multiple users can message one tool-enabled agent, they share that delegated tool      │
│  authority.                                                                                │
│                                                                                            │
│  If you’re not comfortable with security hardening and access control, don’t run           │
│  OpenClaw.                                                                                 │
│  Ask someone experienced to help before enabling tools or exposing it to the internet.     │
│                                                                                            │
│  Recommended baseline:                                                                     │
│  - Pairing/allowlists + mention gating.                                                    │
│  - Multi-user/shared inbox: split trust boundaries (separate gateway/credentials, ideally  │
│    separate OS users/hosts).                                                               │
│  - Sandbox + least-privilege tools.                                                        │
│  - Shared inboxes: isolate DM sessions (`session.dmScope: per-channel-peer`) and keep      │
│    tool access minimal.                                                                    │
│  - Keep secrets out of the agent’s reachable filesystem.                                   │
│  - Use the strongest available model for any bot with tools or untrusted inboxes.          │
│                                                                                            │
│  Run regularly:                                                                            │
│  openclaw security audit --deep                                                            │
│  openclaw security audit --fix                                                             │
│                                                                                            │
│  Must read: https://docs.openclaw.ai/gateway/security                                      │
│                                                                                            │
├────────────────────────────────────────────────────────────────────────────────────────────╯
│
◇  I understand this is personal-by-default and shared/multi-user use requires lock-down. Continue?
│  Yes
│
◇  Onboarding mode
│  Manual
│
◇  What do you want to set up?
│  Local gateway (this machine)
│
◇  Workspace directory
│  /home/ubuntu/.openclaw/workspace
│
◇  Model/auth provider
│  Anthropic
│
◇  Anthropic auth method
│  Anthropic API key
│
◇  How do you want to provide this API key?
│  Paste API key now
│
◇  Enter Anthropic API key
│  <paste_api_key_here>
│
◇  Default model
│  Keep current (anthropic/claude-sonnet-4-6)
│
◇  Gateway port
│  18789
│
◇  Gateway bind
│  Loopback (127.0.0.1)
│
◇  Gateway auth
│  Token
│
◇  Tailscale exposure
│  Off
│
◇  Gateway token (blank to generate)
│
│
◇  Channel status ────────────────────────────╮
│                                             │
│  Telegram: needs token                      │
│  WhatsApp (default): not linked             │
│  Discord: needs token                       │
│  Slack: needs tokens                        │
│  Signal: needs setup                        │
│  signal-cli: missing (signal-cli)           │
│  iMessage: needs setup                      │
│  imsg: missing (imsg)                       │
│  IRC: not configured                        │
│  Google Chat: not configured                │
│  Feishu: install plugin to enable           │
│  Google Chat: install plugin to enable      │
│  Nostr: install plugin to enable            │
│  Microsoft Teams: install plugin to enable  │
│  Mattermost: install plugin to enable       │
│  Nextcloud Talk: install plugin to enable   │
│  Matrix: install plugin to enable           │
│  BlueBubbles: install plugin to enable      │
│  LINE: install plugin to enable             │
│  Zalo: install plugin to enable             │
│  Zalo Personal: install plugin to enable    │
│  Synology Chat: install plugin to enable    │
│  Tlon: install plugin to enable             │
│                                             │
├─────────────────────────────────────────────╯
│
◇  Configure chat channels now?
│  Yes
│
◇  How channels work ───────────────────────────────────────────────────────────────────────╮
│                                                                                           │
│  DM security: default is pairing; unknown DMs get a pairing code.                         │
│  Approve with: openclaw pairing approve <channel> <code>                                  │
│  Public DMs require dmPolicy="open" + allowFrom=["*"].                                    │
│  Multi-user DMs: run: openclaw config set session.dmScope "per-channel-peer" (or          │
│  "per-account-channel-peer" for multi-account channels) to isolate sessions.              │
│  Docs: channels/pairing              │
│                                                                                           │
│  Telegram: simplest way to get started — register a bot with @BotFather and get going.    │
│  WhatsApp: works with your own number; recommend a separate phone + eSIM.                 │
│  Discord: very well supported right now.                                                  │
│  IRC: classic IRC networks with DM/channel routing and pairing controls.                  │
│  Google Chat: Google Workspace Chat app with HTTP webhook.                                │
│  Slack: supported (Socket Mode).                                                          │
│  Signal: signal-cli linked device; more setup (David Reagans: "Hop on Discord.").         │
│  iMessage: this is still a work in progress.                                              │
│  Feishu: 飞书/Lark enterprise messaging with doc/wiki/drive tools.                        │
│  Nostr: Decentralized protocol; encrypted DMs via NIP-04.                                 │
│  Microsoft Teams: Bot Framework; enterprise support.                                      │
│  Mattermost: self-hosted Slack-style chat; install the plugin to enable.                  │
│  Nextcloud Talk: Self-hosted chat via Nextcloud Talk webhook bots.                        │
│  Matrix: open protocol; install the plugin to enable.                                     │
│  BlueBubbles: iMessage via the BlueBubbles mac app + REST API.                            │
│  LINE: LINE Messaging API bot for Japan/Taiwan/Thailand markets.                          │
│  Zalo: Vietnam-focused messaging platform with Bot API.                                   │
│  Zalo Personal: Zalo personal account via QR code login.                                  │
│  Synology Chat: Connect your Synology NAS Chat to OpenClaw with full agent capabilities.  │
│  Tlon: decentralized messaging on Urbit; install the plugin to enable.                    │
│                                                                                           │
├───────────────────────────────────────────────────────────────────────────────────────────╯
│
◇  Select a channel
│  Slack (Socket Mode)
│
◇  Slack bot display name (used for manifest)
│  OpenClaw
│
◇  Slack socket mode tokens ─────────────────────────────────────────────╮
│                                                                        │
│  1) Slack API → Create App → From scratch                              │
│  2) Add Socket Mode + enable it to get the app-level token (xapp-...)  │
│  3) OAuth & Permissions → install app to workspace (xoxb- bot token)   │
│  4) Enable Event Subscriptions (socket) for message events             │
│  5) App Home → enable the Messages tab for DMs                         │
│  Tip: set SLACK_BOT_TOKEN + SLACK_APP_TOKEN in your env.               │
│  Docs: slack                 │
│                                                                        │
│  Manifest (JSON):                                                      │
│  {                                                                     │
│    "display_information": {                                            │
│      "name": "OpenClaw",                                               │
│      "description": "OpenClaw connector for OpenClaw"                  │
│    },                                                                  │
│    "features": {                                                       │
│      "bot_user": {                                                     │
│        "display_name": "OpenClaw",                                     │
│        "always_online": false                                          │
│      },                                                                │
│      "app_home": {                                                     │
│        "messages_tab_enabled": true,                                   │
│        "messages_tab_read_only_enabled": false                         │
│      },                                                                │
│      "slash_commands": [                                               │
│        {                                                               │
│          "command": "/openclaw",                                       │
│          "description": "Send a message to OpenClaw",                  │
│          "should_escape": false                                        │
│        }                                                               │
│      ]                                                                 │
│    },                                                                  │
│    "oauth_config": {                                                   │
│      "scopes": {                                                       │
│        "bot": [                                                        │
│          "chat:write",                                                 │
│          "channels:history",                                           │
│          "channels:read",                                              │
│          "groups:history",                                             │
│          "im:history",                                                 │
│          "mpim:history",                                               │
│          "users:read",                                                 │
│          "app_mentions:read",                                          │
│          "reactions:read",                                             │
│          "reactions:write",                                            │
│          "pins:read",                                                  │
│          "pins:write",                                                 │
│          "emoji:read",                                                 │
│          "commands",                                                   │
│          "files:read",                                                 │
│          "files:write"                                                 │
│        ]                                                               │
│      }                                                                 │
│    },                                                                  │
│    "settings": {                                                       │
│      "socket_mode_enabled": true,                                      │
│      "event_subscriptions": {                                          │
│        "bot_events": [                                                 │
│          "app_mention",                                                │
│          "message.channels",                                           │
│          "message.groups",                                             │
│          "message.im",                                                 │
│          "message.mpim",                                               │
│          "reaction_added",                                             │
│          "reaction_removed",                                           │
│          "member_joined_channel",                                      │
│          "member_left_channel",                                        │
│          "channel_rename",                                             │
│          "pin_added",                                                  │
│          "pin_removed"                                                 │
│        ]                                                               │
│      }                                                                 │
│    }                                                                   │
│  }                                                                     │
│                                                                        │
├────────────────────────────────────────────────────────────────────────╯
│
◇  Enter Slack bot token (xoxb-...)
│  <paste_api_key_here>
│
◇  Enter Slack app token (xapp-...)
│  <paste_api_key_here>
│
◇  Configure Slack channels access?
│  Yes
│
◇  Slack channels access
│  Allowlist (recommended)
│
◇  Slack channels allowlist (comma-separated)
│  zyp-agent
│
◇  Slack channels ─────────────────────────────────────────────────────────────────╮
│                                                                                  │
│  Channel lookup failed; keeping entries as typed. Error: An API error occurred:  │
│  missing_scope                                                                   │
│                                                                                  │
├──────────────────────────────────────────────────────────────────────────────────╯
│
◇  Select a channel
│  Finished
│
◇  Selected channels ────────────────────────────────────────╮
│                                                            │
│  Slack — supported (Socket Mode). Docs:                    │
│  slack  │
│                                                            │
├────────────────────────────────────────────────────────────╯
│
◇  Configure DM access policies now? (default: pairing)
│  No
Updated ~/.openclaw/openclaw.json
Workspace OK: ~/.openclaw/workspace
Sessions OK: ~/.openclaw/agents/main/sessions
│
◇  Skills status ─────────────╮
│                             │
│  Eligible: 5                │
│  Missing requirements: 39   │
│  Unsupported on this OS: 7  │
│  Blocked by allowlist: 0    │
│                             │
├─────────────────────────────╯
│
◇  Configure skills now? (recommended)
│  Yes
│
◇  Install missing skill dependencies
│  🧩 clawhub, 🎛️ eightctl, 🧲 gifgrep, 🐙 github, 🎮 gog, 🧿 oracle, 🧾 summarize
│
◇  Homebrew recommended ──────────────────────────────────────────────────────────╮
│                                                                                 │
│  Many skill dependencies are shipped via Homebrew.                              │
│  Without brew, you'll need to build from source or download releases manually.  │
│                                                                                 │
├─────────────────────────────────────────────────────────────────────────────────╯
│
◇  Show Homebrew install command?
│  Yes
│
◇  Homebrew install ─────────────────────────────────────────────────────╮
│                                                                        │
│  Run:                                                                  │
│  /bin/bash -c "$(curl -fsSL                                            │
│  https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"  │
│                                                                        │
├────────────────────────────────────────────────────────────────────────╯
│
◇  Preferred node manager for skill installs
│  npm
│
◇  Installed clawhub
│
◇  Installed eightctl
│
◇  Installed gifgrep
│
◇  Install failed: github — brew not installed — Homebrew is not installed. Install it from https://brew.sh or install "gh" manually using your system package manager …
Tip: run `openclaw doctor` to review skills + requirements.
Docs: https://docs.openclaw.ai/skills
│
◇  Install failed: gog — brew not installed — Homebrew is not installed. Install it from https://brew.sh or install "steipete/tap/gogcli" manually using your system…
Tip: run `openclaw doctor` to review skills + requirements.
Docs: https://docs.openclaw.ai/skills
│
◇  Installed oracle
│
◇  Install failed: summarize — brew not installed — Homebrew is not installed. Install it from https://brew.sh or install "steipete/tap/summarize" manually using your sys…
Tip: run `openclaw doctor` to review skills + requirements.
Docs: https://docs.openclaw.ai/skills
│
◇  Set GOOGLE_PLACES_API_KEY for goplaces?
│  Yes
│
◇  Enter GOOGLE_PLACES_API_KEY
│  <paste_api_key_here>
│
◇  Set GEMINI_API_KEY for nano-banana-pro?
│  Yes
│
◇  Enter GEMINI_API_KEY
│  <paste_api_key_here>
│
◇  Set NOTION_API_KEY for notion?
│  Yes
│
◇  Enter NOTION_API_KEY
│  <paste_api_key_here>
│
◇  Set OPENAI_API_KEY for openai-image-gen?
│  Yes
│
◇  Enter OPENAI_API_KEY
│  <paste_api_key_here>
│
◇  Set OPENAI_API_KEY for openai-whisper-api?
│  Yes
│
◇  Enter OPENAI_API_KEY
│  <paste_api_key_here>
│
◇  Set ELEVENLABS_API_KEY for sag?
│  Yes
│
◇  Enter ELEVENLABS_API_KEY
│  <paste_api_key_here>
│
◇  Hooks ──────────────────────────────────────────────────────────────────╮
│                                                                          │
│  Hooks let you automate actions when agent commands are issued.          │
│  Example: Save session context to memory when you issue /new or /reset.  │
│                                                                          │
│  Learn more: https://docs.openclaw.ai/automation/hooks                   │
│                                                                          │
├──────────────────────────────────────────────────────────────────────────╯
│
◇  Enable hooks?
│  📝 command-logger, 💾 session-memory
│
◇  Hooks Configured ────────────────────────────────╮
│                                                   │
│  Enabled 2 hooks: session-memory, command-logger  │
│                                                   │
│  You can manage hooks later with:                 │
│    openclaw hooks list                            │
│    openclaw hooks enable <name>                   │
│    openclaw hooks disable <name>                  │
│                                                   │
├───────────────────────────────────────────────────╯
Config overwrite: /home/ubuntu/.openclaw/openclaw.json (sha256 577695db522d902816785f62712c0420ae3bf94d4e8c9aa2e415ac1170076f05 -> 58ac884d7a30136ec51abb7056946a7943db5e36943d5c0ba5fa971a38b49ed2, backup=/home/ubuntu/.openclaw/openclaw.json.bak)
│
◇  Systemd ────────────────────────────────────────────────────────────────────────────────╮
│                                                                                          │
│  Linux installs use a systemd user service by default. Without lingering, systemd stops  │
│  the user session on logout/idle and kills the Gateway.                                  │
│  Enabling lingering now (may require sudo; writes /var/lib/systemd/linger).              │
│                                                                                          │
├──────────────────────────────────────────────────────────────────────────────────────────╯
│
◇  Systemd ───────────────────────────────╮
│                                         │
│  Enabled systemd lingering for ubuntu.  │
│                                         │
├─────────────────────────────────────────╯
│
◇  Install Gateway service (recommended)
│  Yes
│
◇  Gateway service runtime
│  Node (recommended)
│
◐  Installing Gateway service…
Installed systemd service: /home/ubuntu/.config/systemd/user/openclaw-gateway.service
◇  Gateway service installed.
│
◇
Slack: ok (942ms)
Agents: main (default)
Heartbeat interval: 30m (main)
Session store (main): /home/ubuntu/.openclaw/agents/main/sessions/sessions.json (0 entries)
│
◇  Optional apps ────────────────────────╮
│                                        │
│  Add nodes for extra features:         │
│  - macOS app (system + notifications)  │
│  - iOS app (camera/canvas)             │
│  - Android app (camera/canvas)         │
│                                        │
├────────────────────────────────────────╯
│
◇  Control UI ─────────────────────────────────────────────────────────────────────╮
│                                                                                  │
│  Web UI: http://127.0.0.1:18789/                                                 │
│  Web UI (with token):                                                            │
│  http://127.0.0.1:18789/#token=<generated_token_shown_here>                      │
│  Gateway WS: ws://127.0.0.1:18789                                                │
│  Gateway: reachable                                                              │
│  Docs: https://docs.openclaw.ai/web/control-ui                                   │
│                                                                                  │
├──────────────────────────────────────────────────────────────────────────────────╯
│
◇  Start TUI (best option!) ─────────────────────────────────╮
│                                                            │
│  This is the defining action that makes your agent you.    │
│  Please take your time.                                    │
│  The more you tell it, the better the experience will be.  │
│  We will send: "Wake up, my friend!"                       │
│                                                            │
├────────────────────────────────────────────────────────────╯
│
◇  Token ─────────────────────────────────────────────────────────────────────────────────╮
│                                                                                         │
│  Gateway token: shared auth for the Gateway + Control UI.                               │
│  Stored in: ~/.openclaw/openclaw.json (gateway.auth.token) or OPENCLAW_GATEWAY_TOKEN.   │
│  View token: openclaw config get gateway.auth.token                                     │
│  Generate token: openclaw doctor --generate-gateway-token                               │
│  Web UI stores a copy in this browser's localStorage (openclaw.control.settings.v1).    │
│  Open the dashboard anytime: openclaw dashboard --no-open                               │
│  If prompted: paste the token into Control UI settings (or use the tokenized dashboard  │
│  URL).                                                                                  │
│                                                                                         │
├─────────────────────────────────────────────────────────────────────────────────────────╯
│
◇  How do you want to hatch your bot?
│  Hatch in TUI (recommended)
 openclaw tui - ws://127.0.0.1:18789 - agent main - session main

 session agent:main:main


 Wake up, my friend!


 Hey! Good to meet you. I just came online — fresh start, blank slate, no memory yet.

 Looks like we haven't been introduced. So let's fix that.

 Who am I? That's kind of up to us. I need a name, a vibe, maybe an emoji. I have some ideas if you want suggestions, or you can just tell me what feels right.

 And who are you? What should I call you?
```

N.B. I was installing `OpenClaw` on my Linux server so actually the answer to the question `Show Homebrew install command?` should be `No` but the initial answer I gave during the installation is `Yes` as shown above. This turns out to be fine -- just some installation failure while trying installing those brew relevant Skills. For the API keys configurations for various tools/Skills, most of them are straightforward to obtain. The only one that needs a special mention here is the `Notion API key`. To get it, we need to go to [https://www.notion.so/profile/integrations/public](https://www.notion.so/profile/integrations/public) and then go to `Internal integration` under `Build` (see the left-hand side items in the page) and create a new integration there.

After the successful installation, the web UI is accessible from `http://localhost:18789`. Here, I am using `nginx + Cloudflare` for the secure connection. Refer to the instructions in Ref. [1-3] for the setup. In my case, after the configuration, the web UI can be accessed via [htts://openclaw.iris-home.net](htts://openclaw.iris-home.net). First time visiting the web UI, we are expected to see some errors on the web interface saying the `Status` is `Offline`. This is probably because we haven't set up the `Gateway Token`. To obtain the token, we can refer to the `Control UI` session in the installation process as presented  above. Or, on terminal, we can do `openclaw dashboard --no-open` to see the token. Then we go to the web UI and paste the token in `Gateway Token`, followed by clicking on the `Connect` button. If error occurs, we can go to the OpenClaw configuration file located at `~/.openclaw/openclaw.json` and look for `allowedOrigins` under the `gateway/controlUI` session. Then we can put in the domain like below,

```
...
  "gateway": {
    "port": 18789,
    "mode": "local",
    "bind": "loopback",
    "controlUi": {
      "allowedOrigins": [
        "http://localhost:18789",
        "https://openclaw.iris-2020.us"
      ]
    },
...
```

After making the change, we run `openclaw gateway restart` to restart the OpenClaw gateway. Now going back to the web UI, we should see something different -- still there is expected error message but the message should now specify how we should move forward. Fundamentally, this is the security mechanism implemented with OpenClaw -- as we will notice, there is no login mechanism coming with the web UI so there has to be a security mechanism in place to prevent unauthorized access to the web UI. So, every time we try a new browser to get access to the web UI, we are expected to see the same error message telling us what to do. Basically, a fresh new access to the web UI will trigger a request to the server and on the server side, we can run `openclaw devices list` on the terminal to see the request. Then we can copy the request ID from the table (see the first column from the left) and run `openclaw devices approve [here_we_put_down_the_request_id_without_the_square_bracket]`. Now going back to the web UI, we should see that everything is OK.

During the installation, we were asked to set up channels (Slack, iMessage, etc.) for communicating with `OpenClaw`. I am using `Slack` here. To set up the `Slack` connection, we would need the app and bot API keys. Here we can follow the steps as following for the setup. First, go to api.slack.com/apps and click "Create New App" → "From scratch", and then give it a name and select the workspace for the app. `OpenClaw` supports two modes for the `Slack` connection and here I am going with the `Socket Mode`. Go to `Socket Mode` in the `Slack api` page and enable the socket mode by clicking on the switch. Then we need to give it a name and confirm to bring up the API key window where we can copy the API key -- this is the app API needed by `OpenClaw`. Then we go to `OAuth & Permissions`, and in the `Scopes` session, select the `Bot Token Scopes`, including `chat:write`, `channels:history`, `channels:read`, `im:write`, `im:history`, `im:read`, `users:read`, `reactions:read`, `reactions:write`, `files:read`, `files:write`, etc. Then go to the `OAuth Tokens` session in the page to install the app to the workspace and get the `xoxb-...` Bot Token -- this is the bot API required by `OpenClaw`. Finally, we need to go to `Event Subscriptions` in the `Slack api` page and add the following events subscription in the `Subscribe to bot events` session, `app_mention`, `message.channels`, `message.groups`, `message.im`, `message.mpim`. In principle, we should do this `Slack` setup before installing `OpenClaw` so when asked during the installation we can directly put in the required API keys. However in practice, even we do this, initially, it is possible the `Slack` integration would not work. In that case, we just follow the steps here for the `Slack` setup and obtain the app and bot API keys. Then, we can edit the `OpenClaw` configuration file (again, located at `~/.openclaw/openclaw.json` on the server) and edit the `channels` session to be like this,

```
...
  "channels": {
    "slack": {
      "mode": "socket",
      "webhookPath": "/slack/events",
      "enabled": true,
      "botToken": "xoxb-......",
      "appToken": "xapp-......",
      "userTokenReadOnly": true,
      "allowBots": true,
      "groupPolicy": "allowlist",
      "streaming": "partial",
      "nativeStreaming": true,
      "channels": {
        "zyp-agent": {
          "enabled": true,
          "allow": true,
          "requireMention": false,
          "allowBots": true
        }
      }
    }
  },
...
```

where `zyp-agent` is my `Slack` channel name to incorporate the `OpenClaw` app (this is the name I gave for the app) we just created.

<br>

References
===

[1] [https://iris2020.net/2023-12-25-vps_docker_services/](https://iris2020.net/2023-12-25-vps_docker_services/)

[2] [https://iris2020.net/2025-08-18-cloudcone_setup/](https://iris2020.net/2025-08-18-cloudcone_setup/)

[3] [https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/](https://iris2020.net/2024-11-03-n8n_notion_slack_workflow/)

[4] [https://www.reddit.com/r/clawdbot/comments/1qrwovq/unauthorized_gateway/](https://www.reddit.com/r/clawdbot/comments/1qrwovq/unauthorized_gateway/)