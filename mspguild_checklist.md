# 🟢 MSPGuild Deployment Checklist
**Resume Date:** 2025/12/09  
**Time:** 11:00 AM CST  

## 1️⃣ Update `compose.yaml`
- [ ] Update Traefik host rules: main site → `mspguild.tech`
- [ ] Update Adminer host rule → `adminer.mspguild.tech`
- [ ] Double-check any other domain references

## 2️⃣ Wipe Traefik ACME storage
- [ ] If using Docker volume:
```bash
docker volume rm mspguild_traefik_letsencrypt
 If using mounted path:

bash
Copy code
sudo rm -rf /var/www/mspguild/.local/share/letsencrypt
sudo rm -rf /var/www/mspguild/letsencrypt
3️⃣ Restart Docker stack
 Stop stack: docker compose down

 Start stack: docker compose up -d

4️⃣ Confirm certificates
 Check Traefik dashboard/logs

 Ensure mspguild.tech cert generated

 Ensure adminer.mspguild.tech cert generated

5️⃣ Test site and Adminer
 Open browser → https://mspguild.tech

 Open browser → https://adminer.mspguild.tech

6️⃣ Verify environment variables
 .env → all domain references mspguild.tech

 .env.example → all domain references mspguild.tech

7️⃣ Phase 1 Complete
 Confirm all steps ✅

 Ready for Phase 2 / CI/CD setup 🎉
---