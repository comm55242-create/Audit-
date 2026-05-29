package com.melcomgroup.zahed.melcomshopaudit;

import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

/* JADX INFO: loaded from: classes.dex */
public class LoginActivity extends AppCompatActivity {
    private Button Button_logout;
    private Button login;
    private EditText username;
    private EditText zone;

    @Override // android.support.v7.app.AppCompatActivity, android.support.v4.app.FragmentActivity, android.support.v4.app.BaseFragmentActivityGingerbread, android.app.Activity
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);
        this.username = (EditText) findViewById(R.id.editText_username);
        this.zone = (EditText) findViewById(R.id.editText_zone);
        this.Button_logout = (Button) findViewById(R.id.button_exit);
        this.Button_logout.setOnClickListener(new View.OnClickListener() { // from class: com.melcomgroup.zahed.melcomshopaudit.LoginActivity.1
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
        if (TextUtils.isEmpty(this.username.getText().toString())) {
            Toast.makeText(this, "Enter user name ", 1).show();
            return;
        }
        if (TextUtils.isEmpty(this.zone.getText().toString())) {
            Toast.makeText(this, "Enter ZONE /Rack number", 1).show();
            return;
        }
        Intent i1 = new Intent(this, (Class<?>) MainActivity.class);
        i1.putExtra("USERNAME", this.username.getText().toString());
        i1.putExtra("ZONE", this.zone.getText().toString());
        i1.addFlags(335577088);
        startActivity(i1);
    }

    public void Onexit1(View View) {
        finish();
        System.exit(0);
    }
}
