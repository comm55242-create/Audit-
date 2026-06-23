package com.melcomgroup.zahed.melcomshoprecount;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import android.app.ProgressDialog;
import android.os.AsyncTask;
import android.os.Handler;
import org.json.JSONObject;
import android.net.Uri;

/* JADX INFO: loaded from: classes.dex */
public class LoginActivity extends AppCompatActivity {
    private Button Button_logout;
    private Button login;
    private EditText username;
    private EditText zone;
    private ProgressDialog progressDialog;
    private Handler handler = new Handler();
    private String currentUser = "";
    private String currentZone = "";

    @Override // android.support.v7.app.AppCompatActivity, android.support.v4.app.FragmentActivity, android.support.v4.app.BaseFragmentActivityGingerbread, android.app.Activity
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        this.username = (EditText) findViewById(R.id.editText_username);
        this.zone = (EditText) findViewById(R.id.editText_zone);
        this.Button_logout = (Button) findViewById(R.id.button_exit);
        this.Button_logout.setOnClickListener(new View.OnClickListener() { // from class: com.melcomgroup.zahed.melcomshoprecount.LoginActivity.1
            @Override // android.view.View.OnClickListener
            public void onClick(View v) {
                Intent myIntent = new Intent(LoginActivity.this, (Class<?>) LoginActivity.class);
                LoginActivity.this.startActivity(myIntent);
                LoginActivity.this.finish();
                LoginActivity.this.onBackPressed();
            }
        });
    }

    public void OnLogin(View View) {
        String uName = this.username.getText().toString().trim();
        String zName = this.zone.getText().toString().trim();

        if (uName.contains(",")) {
            String[] parts = uName.split(",");
            if (parts.length >= 2) {
                uName = parts[0];
                zName = parts[1];
                this.username.setText(uName);
                this.zone.setText(zName);
            }
        } else if (zName.contains(",")) {
            String[] parts = zName.split(",");
            if (parts.length >= 2) {
                uName = parts[0];
                zName = parts[1];
                this.username.setText(uName);
                this.zone.setText(zName);
            }
        }

        if (TextUtils.isEmpty(uName)) {
            Toast.makeText(this, "Enter user name", Toast.LENGTH_SHORT).show();
            return;
        }
        if (TextUtils.isEmpty(zName)) {
            Toast.makeText(this, "Enter ZONE /Rack number", Toast.LENGTH_SHORT).show();
            return;
        }
        
        this.currentUser = uName;
        this.currentZone = zName;
        
        // OLD BEHAVIOR: Wait for manager approval
        this.progressDialog = new ProgressDialog(this);
        this.progressDialog.setMessage("Waiting for Manager Approval...");
        this.progressDialog.setCancelable(false);
        this.progressDialog.show();
        checkApproval();
    }

    private void checkApproval() {
        new ApprovalTask().execute(currentUser, currentZone);
    }

    private class ApprovalTask extends AsyncTask<String, Void, String> {
        @Override
        protected String doInBackground(String... params) {
            String uName = params[0];
            String zName = params[1];
            try {
                Uri.Builder builder = new Uri.Builder()
                        .appendQueryParameter("shop_code", "0")
                        .appendQueryParameter("user_name", uName)
                        .appendQueryParameter("zone_name", zName);
                String query = builder.build().getEncodedQuery();
                String result = network.post(network.BASE_URL + "request_approval.php", query);
                if (result != null) {
                    JSONObject json = new JSONObject(result);
                    return json.getString("status");
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
            return "ERROR";
        }

        @Override
        protected void onPostExecute(String status) {
            if ("APPROVED".equals(status)) {
                if (progressDialog != null && progressDialog.isShowing()) {
                    progressDialog.dismiss();
                }
                Intent i1 = new Intent(LoginActivity.this, MainActivity.class);
                i1.putExtra("USERNAME", currentUser);
                i1.putExtra("ZONE", currentZone);
                i1.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(i1);
            } else if ("REJECTED".equals(status)) {
                if (progressDialog != null && progressDialog.isShowing()) {
                    progressDialog.dismiss();
                }
                Toast.makeText(LoginActivity.this, "Manager REJECTED your request.", Toast.LENGTH_LONG).show();
            } else {
                handler.postDelayed(new Runnable() {
                    @Override
                    public void run() {
                        checkApproval();
                    }
                }, 3000);
            }
        }
    }

    public void Onexit1(View View) {
        finish();
        System.exit(0);
    }
}
