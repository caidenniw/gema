# cc-proxy — jembatan CommandCode plan Go ke GEMA AI

Service kecil (Node) yang menerjemahkan request format OpenAI ke endpoint
`/alpha/generate` milik CommandCode. Dipakai supaya **key plan Go** (yang
diblokir di `/provider/v1/*` dengan error `403 upgrade_required`) tetap bisa
dipakai oleh GEMA.

## Kenapa perlu ini

CommandCode punya dua permukaan API:

| Endpoint | Format | Plan yang boleh |
| --- | --- | --- |
| `/provider/v1/chat/completions` | OpenAI-compatible | GOAT / Pro / Max / Team / Provider — **BUKAN Go** |
| `/alpha/generate` | custom (punya CLI sendiri) | **semua plan, termasuk Go** |

`/alpha/generate` hanya menerima request yang membawa header identitas CLI
(`User-Agent: commandcode-cli/...`, `x-command-code-version`, `x-cli-environment`,
`x-session-id`, `x-project-slug`, `traceparent`). Proxy ini yang menyiapkan
header tersebut.

## Environment variable

| Variable | Wajib | Isi |
| --- | --- | --- |
| `CC_API_KEY` | ya | API key CommandCode (key plan Go milik client) |
| `CC_CLI_VERSION` | tidak | versi CLI yang dikirim ke upstream (default `0.40.3`, minimal `0.18.10`) |
| `CC_API_BASE` | tidak | default `https://api.commandcode.ai` |

## Deploy di Railway (private network, tidak publik)

1. Railway project `miraculous-love` → **+ New** → **GitHub Repo** → pilih repo `caidenniw/gema`
2. Service baru itu → **Settings → Source → Root Directory** → isi `cc-proxy`
3. Tab **Variables** → tambah `CC_API_KEY` = key CommandCode plan Go
4. **Settings → Networking** → **jangan** generate public domain (biar tidak ada yang bisa pakai credit client)
5. Service `gema` → **Variables**:
   - `DEEPSEEK_URL` = `http://cc-proxy.railway.internal:8790/v1/chat/completions`
   - `DEEPSEEK_API_KEY` = `proxy-managed`
   - `DEEPSEEK_MODEL` = `deepseek/deepseek-v4-flash`

Private network butuh service `gema` dan `cc-proxy` di project + environment yang sama.

## Jalankan di laptop (buat development lokal)

`start-proxy.bat` di root repo (gitignored, berisi key) lalu jalankan. Setelah itu:

- `DEEPSEEK_URL` lokal = `http://127.0.0.1:8790/v1/chat/completions`
- `DEEPSEEK_API_KEY` = `proxy-managed` (proxy yang menyuntik key asli)

## Catatan risiko

Ini jalur **unofficial**. Source proxy sendiri menulis bahwa server CommandCode
memeriksa header dan menolak request yang terlihat seperti proxy
(`Proxy use detected`). Plan Go dijual sebagai CLI-only, jadi memakai jalur ini
berisiko akun/key disuspend. Kalau butuh jalur resmi, upgrade plan ke GOAT
($10/mo, sudah termasuk API access + $70 credits) lalu arahkan GEMA langsung ke
`https://api.commandcode.ai/provider/v1/chat/completions` tanpa proxy.
