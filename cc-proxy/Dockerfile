FROM node:22-alpine

# Command Code API Proxy — jembatan OpenAI-compatible -> /alpha/generate
# Dipakai supaya key plan Go (yang diblokir di /provider/v1) tetap bisa dipakai.
RUN npm i -g commandcode-api-proxy@0.4.1

# Bind IPv6 dual-stack supaya bisa diakses dari service lain via Railway private network
# (Railway private networking pakai IPv6, host `*.railway.internal`)
ENV HOST=::
ENV PORT=8790
ENV CC_API_BASE=https://api.commandcode.ai

EXPOSE 8790

# Flag CLI menang atas env, jadi port dikunci 8790 supaya URL dari GEMA stabil
CMD ["commandcode-api-proxy", "--host", "::", "--port", "8790"]
